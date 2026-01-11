<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PriorityActionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('priority_actions')->delete();

        \DB::table('priority_actions')->insert(array (
            0 =>
            array (
                'id' => '1.1',
                'short_name' => '1.1 - Promote integration of AE approaches in policies and plans',
                'name' => 'Promote the integration of agroecological approaches in policies and plans that address agriculture and food system challenges in the local context by strengthening the resilience of food systems.',
                'recommendation_id' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 =>
            array (
                'id' => '1.2',
                'short_name' => '1.2 - Strengthen public policies (markets)',
                'name' => 'Strengthen public policies to harness market mechanisms to enable sustainable agriculture and food systems by considering economic, environmental, and social, including public health, externalities, trade-offs and synergies. ',
                'recommendation_id' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            2 =>
            array (
                'id' => '1.3',
                'short_name' => '1.3 - Enhance cross-sectoral policy coherence',
                'name' => 'Enhance policy coherence and coordination of agroecological approaches across sectors such as health, agriculture, environment, trade, and finance, which impact food systems and nutrition outcomes. ',
                'recommendation_id' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            3 =>
            array (
                'id' => '1.4',
                'short_name' => '1.4 - Strengthen policies to address gender inequality',
                'name' => 'Strengthen policies, programmes and actions that address root causes of gender inequality, in particular laws and policies to support inter alia equal access to natural resources, finance and public services, respecting and protecting women\'s knowledge.',
                'recommendation_id' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            4 =>
            array (
                'id' => '2.1',
                'short_name' => '2.1 - Encourage data collection and analysis at national level',
                'name' => 'Encourage data collection and analysis at national level, documentation of lessons learned and information sharing at all levels to support evaluation of the performance of agroecological approaches.',
                'recommendation_id' => 2,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            5 =>
            array (
                'id' => '2.2',
                'short_name' => '2.2 - Holistic assessments',
                'name' => 'Undertake holistic assessments of employment conditions, dietary diversity, nutritional outcomes, women\'s empowerment, and income stability. ',
                'recommendation_id' => 2,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            6 =>
            array (
                'id' => '2.3',
                'short_name' => '2.3 - Assess and document the contribution of agroecology to FSN',
                'name' => 'Assess and document the contribution of agroecology to FSN at national and global levels, working in collaboration with member countries. ',
                'recommendation_id' => 2,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            7 =>
            array (
                'id' => '2.4',
                'short_name' => '2.4 - Sharing of AE principles and practices',
                'name' => 'Encourage data collection, documentation and information sharing on agroecological principles and practices to foster transitions toward sustainable food systems.',
                'recommendation_id' => 2,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            8 =>
            array (
                'id' => '3.1',
                'short_name' => '3.1 - Advocate for diversified production systems',
                'name' => 'Raise awareness and advocate for the importance of diversified production systems and healthy diets that integrate livestock, aquaculture, cropping and agroforestry, as appropriate, to enhance resilient livelihoods and promote sustainable production for healthy diets.',
                'recommendation_id' => 3,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            9 =>
            array (
                'id' => '3.2',
                'short_name' => '3.2 - Promote alternatives to chemical pesticides',
                'name' => 'Promote, based on agroecological approaches, alternatives to chemical pesticides and greater integration of biodiversity for food and agriculture and especially, encourage the removal of highly hazardous pesticides.',
                'recommendation_id' => 3,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            10 =>
            array (
                'id' => '3.3',
                'short_name' => '3.3 - Responsible investment and social innovation in MSMEs',
                'name' => 'Strengthen responsible investment and social innovation in micro, small and medium-sized enterprises that support sustainable agriculture and food systems and retain value locally, especially small-scale producers and women.',
                'recommendation_id' => 3,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            11 =>
            array (
                'id' => '3.4',
            'short_name' => '3.4 - Market and social innovations linking urban and rural',
            'name' => 'Support market and social innovations (including use of digital technologies) that strengthen linkages between urban communities and rural farmers by capturing a high proportion of the value of production locally.',
                'recommendation_id' => 3,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            12 =>
            array (
                'id' => '3.5',
            'short_name' => '3.5 - Awareness of AE contribution to SDGs and KJWA',
            'name' => 'Raise awareness of the contribution of agroecological approaches to achieving most of the SDGs and to advancing the Koronivia Joint Work on Agriculture (KJWA) to achieve sustainable and climate-resilient food systems at the national and global levels.',
                'recommendation_id' => 3,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            13 =>
            array (
                'id' => '3.6',
                'short_name' => '3.6 - Recognize value of and strengthen support for AE approaches',
                'name' => 'Consistent with national contexts, recognize the value of and strengthen support for agroecological approaches that promote recycling, regeneration of soil health, optimizing and reducing, where appropriate, reliance on external inputs.',
                'recommendation_id' => 3,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            14 =>
            array (
                'id' => '4.1',
                'short_name' => '4.1 - Increased resource allocation in public and private research',
                'name' => 'Considering national contexts and regulations, encourage increased resource allocation in public research and responsible investments in private research that promotes evidence-based balanced investment towards enhanced support for agroecological approaches.',
                'recommendation_id' => 4,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            15 =>
            array (
                'id' => '4.2',
                'short_name' => '4.2 - Transdisciplinary research and innovation platforms',
                'name' => 'Develop and support transdisciplinary research and innovation platforms that foster co-learning between researchers and practitioners, especially through farmer-to-farmer networks and communities of practice.',
                'recommendation_id' => 4,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            16 =>
            array (
                'id' => '4.3',
                'short_name' => '4.3 - Responsible investment in participatory research and innovation',
                'name' => 'Promote and enable, responsible investment in participatory research and innovation on agroecological approaches, especially to address the specific needs of vulnerable groups, with their active engagement.',
                'recommendation_id' => 4,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            17 =>
            array (
                'id' => '4.5',
                'short_name' => '4.5 - Horizontal sharing of knowledge and experiences',
                'name' => 'Support horizontal sharing of knowledge and experiences building on existing producer organizations and networks with a focus on women, youth, local and indigenous communities.',
                'recommendation_id' => 4,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            18 =>
            array (
                'id' => '5.1',
                'short_name' => '5.1 - Property rights for small-scale producers and indigenous peoples',
                'name' => 'Embed property rights for small-scale producers and indigenous peoples through formal legal and regulatory frameworks.',
                'recommendation_id' => 5,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            19 =>
            array (
                'id' => '5.2',
                'short_name' => '5.2 - Participation of marginalized and vulnerable groups',
                'name' => 'Ensure participation of marginalized and vulnerable groups at all levels of decision-making in the food system',
                'recommendation_id' => 5,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            20 =>
            array (
                'id' => '5.3',
                'short_name' => '5.3 - Empowerment of women small-scale food producers and family farmers',
                'name' => 'Promote the empowerment of women, particularly small-scale food producers and family farmers, and their organizations, by supporting collective action, negotiation and leadership skills, to increase access to and equity in the control over land and natural resources, according to national legislation. ',
                'recommendation_id' => 5,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            21 =>
            array (
                'id' => '5.4',
                'short_name' => '5.4 - Linkages between urban communities and food production systems',
                'name' => 'Strengthen linkages between urban communities and food production systems that favour transitions towards sustainable food systems by including consumer cooperatives and multi-stakeholder platforms focused on local and regional markets.',
                'recommendation_id' => 5,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));


    }
}
