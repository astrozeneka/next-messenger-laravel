<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ConfusingRythmesController extends Controller
{
    public function getStudySet(Request $request)
    {
        $request->validate([
            'level' => 'required|integer|min:1|max:32'
        ]);
        Log::info("Received request for study set", ['level' => $request->input('level')]);

        $level = intval($request->input('level'));

        try {
            $confusingRhymes = DB::select("
                SELECT t.*
                FROM confusing_rhymes t
                JOIN (
                    SELECT id 
                    FROM confusing_rhymes
                    ORDER BY RAND()
                    LIMIT 100
                ) r ON t.id = r.id
                ORDER BY RAND()
                LIMIT 100
            ");

            // Debug: Check if we have data
            if (empty($confusingRhymes)) {
                return response()->json(['debug' => 'No data found in confusing_rhymes table'], 404);
            }

            $studySet = [];

            foreach ($confusingRhymes as $row) {
                $sameRhymes = json_decode($row->same_rhymes, true);
                $similarRhymes = json_decode($row->similar_rhymes, true);
                
                if (count($sameRhymes) < 4 || count($similarRhymes) < 4) {
                    continue;
                }

                usort($sameRhymes, function($a, $b) {
                    return $b['f'] <=> $a['f'];
                });
                usort($similarRhymes, function($a, $b) {
                    return $b['f'] <=> $a['f'];
                });

                $sameRhymes = array_slice($sameRhymes, 0, 4);
                $similarRhymes = array_slice($similarRhymes, 0, 4);

                $averageFrequency = ($row->frequency + 
                    array_sum(array_column($sameRhymes, 'f')) + 
                    array_sum(array_column($similarRhymes, 'f'))) / 
                    (1 + count($sameRhymes) + count($similarRhymes));

                $studySet[] = [
                    'word' => $row->word,
                    'average_frequency' => $averageFrequency,
                    'same_rhymes' => $sameRhymes,
                    'similar_rhymes' => $similarRhymes,
                    'level' => null
                ];
            }

            if (empty($studySet)) {
                return response()->json([
                    'debug' => 'No study items passed filtering',
                    'total_raw_records' => count($confusingRhymes)
                ], 404);
            }

            $frequencies = array_column($studySet, 'average_frequency');
            arsort($frequencies);
            $sortedIndices = array_keys($frequencies);

            $k = min(32, count($studySet));
            $groupSize = intval(count($studySet) / $k);
            $remainder = count($studySet) % $k;

            $startIdx = 0;
            for ($levelIdx = 0; $levelIdx < $k; $levelIdx++) {
                $currentGroupSize = $groupSize + ($levelIdx < $remainder ? 1 : 0);
                $endIdx = $startIdx + $currentGroupSize;
                
                for ($i = $startIdx; $i < $endIdx && $i < count($sortedIndices); $i++) {
                    $studySet[$sortedIndices[$i]]['level'] = $levelIdx + 1;
                }
                
                $startIdx = $endIdx;
            }

            $levelExercises = array_filter($studySet, function($item) use ($level) {
                return $item['level'] === $level;
            });

            if (empty($levelExercises)) {
                return response()->json(['error' => 'No exercises found for this level'], 404);
            }

            return response()->json([
                'level' => $level,
                'exercises' => array_values($levelExercises),
                'total_count' => count($levelExercises)
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Database error occurred'], 500);
        }
    }
}
