@php

    $statements = $record
        ->statements
        ->with('assessmentPriorityAction', 'type')
        ->groupBy('assessmentPriorityAction');

    dd($statements);

@endphp

<x-filament-panels::page
        @class([
            'fi-resource-view-record-page',
            'fi-resource-' . str_replace('/', '-', $this->getResource()::getSlug()),
            'fi-resource-record-' . $record->getKey(),
        ])
>
    <div class="flex flex-col space-y-6">

        <p>

            The global food system is characterized by a confluence of challenges, among which include demographic changes, climate change, land degradation, biodiversity loss, and competition over renewable resources. Agriculture accounts for 60 per cent of terrestrial biodiversity loss, 33 per cent of soil degradation, 61 per cent of the depletion of commercial fish stocks (UNEP, 2016), and an estimated 34 per cent of GHG emissions. Moreover, with an estimated 3.1 billion people unable to afford a healthy diet in 2020 and nearly 670 million people projected to be hungry in 2030 and the growing concerns around equity and social justice, the global food system is moving in the wrong direction and a profound transformation is needed at all scales .
        </p>

        <p>
            It is against this backdrop that the UN Committee on World Food Security (CFS) requested its High Level Panel of Experts (HLPE) on Food Security and Nutrition (FSN) to produce a report on “Agroecological and other innovative approaches for sustainable agriculture and food systems that enhance food security and nutrition” to inform its discussions during the Forty-sixth CFS Plenary Session in October 2019 and adopted a set of policy recommendations at CFS48 in June 2021. There is no universal pathway for achieving food systems transformation. The report and its recommendations present decision-makers with evidence on the potential contribution of agroecological approaches through policies, institutions, research, practices, performance measurements and monitoring frameworks to design and implement sustainable food systems that contribute to FSN.
        </p>
        <p>

            The Transformative Partnership Platform on Agroecology working with the Research and Innovation Working Group of the Agroecology Coalition is developing a tool to track how the five policy recommendations are being implemented, focusing on agroecological rather than other innovative approaches. We reviewed the 62 specific recommendations developed from the five CFS-specific recommendations detailed in CFS (2021) and, defined core areas of focus that best summarize the aims of each of the five policy recommendations and fully embrace the 13 HLPE (2019) agroecological principles.
        </p>
        <p>

            The five policy recommendations are integrative and embrace multiple principles with some repetition across recommendations. For example, Policy recommendation #3: Foster the transition to resilient and diversified sustainable agriculture and food systems through agroecological approaches, encompasses 9 out of the 13 principles; recycling, input reduction, soil health, animal health, biodiversity, economic diversification, social values and diets, fairness and connectivity. The policy recommendations represent an internationally accepted set of high-level policy goals but are watered down from the HLPE report itself as a result of obtaining agreement of a large set of countries during the policy convergence process. The HLPE report emphasizes the importance of lock ins (or outs) constraining agroecological transitions in addition to what is required to foster them. It is important, therefore, to consider things can constrain progress in each area as well actions that promote transition and to not only look at the extent to which these recommendations are implemented but also what more may be being done beyond them. It is also important to obtain a civil society perspective on what is and is not being implemented. We propose a matrix for tracking implementation across CFS’s five policy recommendations, with 23 suggestive priority action areas. The priority actions proposed here are derived as the most parsimonious set around which stakeholders – governments, intergovernmental organizations, civil society, private sector, research and academic institutions – can build policies, programs, and investment and monitoring plans for agroecological approaches that deliver sustainable food systems.
        </p>

        <p>
            In Annex 1, we summarize the focus of the suggested priority actions and encourage stakeholders to identify any gaps and make suggestions for further improvement. Stakeholders, through consultation and consensus, will develop indicators that are relevant to their context and consistent with their transition pathways to track the transformation of food systems. This matrix is intended as an initial assessment and a basis for encouraging dialogue and consensus building to support priority actions that are critical to determining transition pathways that are relevant to local or national contexts. Each item is addressed through four lenses – documenting what is being implemented, what constrains implementation, what is being implemented that goes beyond the recommended action, and a civil society perspective.
        </p>

    </div>

    @foreach(App\Models\PriorityAction::all() as $priorityAction)
        <h3 class="text-2xl font-bold text-gray-900">{{ $priorityAction->name }}</h3>
    @endforeach

</x-filament-panels::page>
