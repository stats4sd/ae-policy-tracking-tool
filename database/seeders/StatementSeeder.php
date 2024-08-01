<?php

namespace Database\Seeders;

use App\Models\AssessmentPriorityAction;
use App\Models\Statement;
use App\Models\Type;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class StatementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Statement::destroy(Statement::all()->pluck('id')->toArray());

        $types = Type::all();

        $fakeStrings = file_get_contents('http://loripsum.net/api/20/short/plaintext');
        $fakeArray = collect(explode("\n", $fakeStrings))->filter(function($string) {
            return strlen($string) > 0;
        })->values();


        foreach(AssessmentPriorityAction::all() as $action) {

            foreach($types as $type) {
                $count = rand(1, 3);

                for($i = 0; $i < $count; $i++) {
                    $action->statements()->create([
                        'type_id' => $type->id,
                        'name' => $fakeArray[rand(0, 19)],
                    ]);

                }
            }

        }
    }
}
