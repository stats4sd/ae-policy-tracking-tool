<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\AssessmentPriorityAction;
use App\Models\PriorityAction;
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


        foreach(PriorityAction::all() as $action) {

            foreach($types as $type) {
                $count = rand(1, 3);

                for($i = 0; $i < $count; $i++) {
                    $action->statements()->create([
                        'type_id' => $type->id,
                        'assessment_id' => Assessment::first()->id,
                        'name' => fake()->sentence(),
                    ]);

                }
            }

        }
    }
}
