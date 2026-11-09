<?php

namespace App\Support\Dashboard;

/**
 * Illustrative data for the dashboard mockups.
 *
 * Everything here is hard-coded so the layouts can be reviewed before the
 * importer and the new extract x priority-action scoring exist. Replace with a
 * real aggregate service (shared with the Excel exports) once data is available.
 *
 * Score distributions are arrays keyed by score value on the proposed -2..4 scale.
 */
class MockDashboardData
{
    /** Proposed score scale. Labels are placeholders until the scale is confirmed. */
    public static function scores(): array
    {
        return [
            -2 => 'Strong perverse incentive',
            -1 => 'Perverse incentive',
            0 => 'Neutral',
            1 => 'Follows (weak)',
            2 => 'Follows',
            3 => 'Goes beyond',
            4 => 'Goes well beyond',
        ];
    }

    public static function dist(int ...$counts): array
    {
        return array_combine(array_keys(self::scores()), $counts);
    }

    /** Direction flag for a PA. Rule is a placeholder for team discussion. */
    public static function direction(array $dist): ?string
    {
        $total = array_sum($dist);
        if ($total === 0) {
            return null;
        }
        $neg = ($dist[-2] + $dist[-1]) / $total;
        $pos = ($dist[1] + $dist[2] + $dist[3] + $dist[4]) / $total;

        if ($neg >= 0.25) {
            if ($pos >= 0.25) {
                return 'mixed';
            }
        }

        return $neg > $pos ? 'negative' : 'positive';
    }

    public static function assessment(): array
    {
        return [
            'title' => 'Uganda 2025',
            'jurisdiction' => 'Uganda',
            'jurisdiction_type' => 'National',
            'year' => 2025,
            'status' => 'Review',
            'language' => 'English',
            'members' => 4,
            'pending_invites' => 1,
            'last_activity' => '3 Nov 2026',
            'summary' => 'Uganda\'s policy framework gives agroecology a clear foothold in agricultural policy and extension, and goes beyond the CFS recommendations on women\'s land rights. Input-subsidy and market policies still favour synthetic inputs, creating perverse incentives under Recommendations 1 and 3. Measurement and research (Recommendations 2 and 4) are thinly covered.',
        ];
    }

    public static function recommendations(): array
    {
        return [
            1 => 'Policy Foundations',
            2 => 'Measuring Progress',
            3 => 'Fostering Transitions',
            4 => 'Co-creation + Co-learning',
            5 => 'Empowering People',
        ];
    }

    /**
     * Priority actions with mock verified-extract score distributions and number of documents with evidence.
     */
    public static function priorityActions(): array
    {
        $pas = [
            ['1.1', 1, 'Promote integration of AE approaches in policies and plans', self::dist(0, 0, 1, 3, 8, 5, 2), 4],
            ['1.2', 1, 'Strengthen public policies (markets)', self::dist(4, 3, 2, 8, 12, 6, 2), 5],
            ['1.3', 1, 'Enhance cross-sectoral policy coherence', self::dist(0, 1, 2, 5, 3, 0, 0), 3],
            ['1.4', 1, 'Strengthen policies to address gender inequality', self::dist(0, 0, 0, 2, 3, 1, 1), 2],
            ['2.1', 2, 'Encourage data collection and analysis at national level', self::dist(0, 0, 1, 3, 1, 0, 0), 2],
            ['2.2', 2, 'Holistic assessments', self::dist(0, 0, 0, 0, 0, 0, 0), 0],
            ['2.3', 2, 'Assess and document the contribution of agroecology to FSN', self::dist(0, 0, 0, 1, 2, 1, 0), 1],
            ['2.4', 2, 'Sharing of AE principles and practices', self::dist(0, 0, 0, 0, 0, 0, 0), 0],
            ['3.1', 3, 'Advocate for diversified production systems', self::dist(0, 0, 2, 6, 9, 4, 1), 4],
            ['3.2', 3, 'Promote alternatives to chemical pesticides', self::dist(3, 2, 0, 1, 1, 0, 0), 2],
            ['3.3', 3, 'Responsible investment and social innovation in MSMEs', self::dist(0, 1, 1, 4, 2, 1, 0), 3],
            ['3.4', 3, 'Market and social innovations linking urban and rural', self::dist(0, 0, 0, 0, 0, 0, 0), 0],
            ['3.5', 3, 'Awareness of AE contribution to SDGs and KJWA', self::dist(0, 0, 0, 0, 0, 0, 0), 0],
            ['3.6', 3, 'Recognize value of and strengthen support for AE approaches', self::dist(0, 0, 0, 2, 3, 1, 0), 2],
            ['4.1', 4, 'Increased resource allocation in public and private research', self::dist(0, 0, 1, 2, 1, 0, 0), 1],
            ['4.2', 4, 'Transdisciplinary research and innovation platforms', self::dist(0, 0, 0, 0, 0, 0, 0), 0],
            ['4.3', 4, 'Responsible investment in participatory research and innovation', self::dist(0, 0, 0, 0, 0, 0, 0), 0],
            ['4.4', 4, 'Horizontal knowledge sharing', self::dist(0, 0, 0, 3, 3, 2, 1), 2],
            ['4.5', 4, 'Horizontal sharing of knowledge and experiences', self::dist(0, 0, 0, 0, 0, 0, 0), 0],
            ['5.1', 5, 'Property rights for small-scale producers and indigenous peoples', self::dist(1, 1, 0, 4, 4, 1, 0), 3],
            ['5.2', 5, 'Participation of marginalized and vulnerable groups', self::dist(0, 0, 0, 0, 0, 0, 0), 0],
            ['5.3', 5, 'Empowerment of women small-scale food producers and family farmers', self::dist(0, 0, 0, 1, 2, 3, 4), 2],
            ['5.4', 5, 'Linkages between urban communities and food production systems', self::dist(0, 0, 0, 0, 0, 0, 0), 0],
        ];

        return collect($pas)->map(fn ($pa) => [
            'id' => $pa[0],
            'recommendation_id' => $pa[1],
            'short_name' => $pa[2],
            'dist' => $pa[3],
            'docs' => $pa[4],
            'extracts' => array_sum($pa[3]),
            'direction' => self::direction($pa[3]),
        ])->all();
    }

    public static function documents(): array
    {
        return [
            ['name' => 'Agriculture and Livestock Policy', 'year' => 1997, 'end_year' => null],
            ['name' => 'National Water Policy (rev. 2022)', 'year' => 2002, 'end_year' => null],
            ['name' => 'National Rice Development Strategy', 'year' => 2009, 'end_year' => 2018],
            ['name' => 'Southern Agricultural Growth Corridor', 'year' => 2010, 'end_year' => null],
            ['name' => 'Climate-Smart Agriculture Programme', 'year' => 2015, 'end_year' => 2025],
        ];
    }

    /** PA drill-down for the selected priority action (1.2). */
    public static function drillDown(): array
    {
        return [
            'pa' => '1.2',
            'name' => 'Strengthen public policies (markets)',
            'by_document' => [
                ['name' => 'Agriculture and Livestock Policy', 'dist' => self::dist(2, 1, 1, 3, 4, 2, 0)],
                ['name' => 'Climate-Smart Agriculture Programme', 'dist' => self::dist(0, 0, 0, 2, 5, 3, 2)],
                ['name' => 'Southern Agricultural Growth Corridor', 'dist' => self::dist(2, 2, 1, 1, 1, 0, 0)],
                ['name' => 'National Rice Development Strategy', 'dist' => self::dist(0, 0, 0, 2, 1, 1, 0)],
                ['name' => 'National Water Policy (rev. 2022)', 'dist' => self::dist(0, 0, 0, 0, 1, 0, 0)],
            ],
            'statements' => [
                ['theme' => 'Public procurement', 'extracts' => 4, 'range' => [2, 3], 'text' => 'School feeding guidelines prioritise local, diversified produce from smallholder farmers.'],
                ['theme' => 'Input markets', 'extracts' => 6, 'range' => [-2, -1], 'text' => 'Input subsidy vouchers are restricted to synthetic fertiliser and hybrid seed packages.'],
                ['theme' => 'Local markets', 'extracts' => 3, 'range' => [1, 2], 'text' => 'District market infrastructure plans mention farmers\' markets but without dedicated budget lines.'],
            ],
        ];
    }

    public static function keyFindings(): array
    {
        return [
            ['pa' => '1.1', 'pa_name' => 'Integration of AE in policies and plans', 'range' => [2, 3], 'extracts' => 7, 'text' => 'The Agriculture and Livestock Policy names agroecology as a production pathway and links it to the national extension service.'],
            ['pa' => '3.2', 'pa_name' => 'Alternatives to chemical pesticides', 'range' => [-2, -1], 'extracts' => 6, 'text' => 'The input subsidy programme favours synthetic fertiliser and pesticide packages, with no support for biological alternatives.'],
            ['pa' => '5.3', 'pa_name' => 'Empowerment of women producers', 'range' => [2, 4], 'extracts' => 5, 'text' => 'The Land Policy sets a quota for women on customary land boards, going further than the CFS recommendation.'],
            ['pa' => '1.2', 'pa_name' => 'Strengthen public policies (markets)', 'range' => [-2, 3], 'extracts' => 37, 'text' => 'Market policy is split: public procurement supports diversified local produce, while input markets reward synthetic inputs.'],
        ];
    }

    /** AE principles, aggregated from linked priority actions (proxy). */
    public static function principles(): array
    {
        return [
            ['name' => 'Recycling', 'dist' => self::dist(0, 0, 0, 0, 0, 0, 0)],
            ['name' => 'Input reduction', 'dist' => self::dist(3, 2, 0, 1, 1, 0, 0)],
            ['name' => 'Soil health', 'dist' => self::dist(3, 2, 0, 1, 1, 0, 0)],
            ['name' => 'Animal health', 'dist' => self::dist(0, 0, 1, 2, 1, 0, 0)],
            ['name' => 'Biodiversity', 'dist' => self::dist(0, 0, 2, 6, 9, 4, 1)],
            ['name' => 'Synergy', 'dist' => self::dist(0, 1, 2, 5, 3, 0, 0)],
            ['name' => 'Economic diversification', 'dist' => self::dist(4, 3, 2, 10, 14, 7, 3)],
            ['name' => 'Co-creation of knowledge', 'dist' => self::dist(0, 0, 1, 6, 5, 3, 1)],
            ['name' => 'Social values and diets', 'dist' => self::dist(0, 0, 0, 1, 2, 1, 0)],
            ['name' => 'Fairness', 'dist' => self::dist(1, 1, 0, 5, 6, 4, 4)],
            ['name' => 'Connectivity', 'dist' => self::dist(4, 4, 3, 12, 14, 7, 2)],
            ['name' => 'Land and natural resource governance', 'dist' => self::dist(1, 1, 0, 4, 4, 1, 0)],
            ['name' => 'Participation', 'dist' => self::dist(0, 1, 0, 5, 6, 4, 4)],
        ];
    }

    /** Document x PA: highest score found (null = no verified evidence). */
    public static function documentHeatmap(): array
    {
        return [
            'pas' => ['1.1', '1.2', '1.3', '1.4', '3.1', '3.2', '5.1', '5.3'],
            'rows' => [
                ['name' => 'Agriculture and Livestock Policy', 'values' => [3, 2, 1, null, 2, -2, 2, 1]],
                ['name' => 'Climate-Smart Agriculture Programme', 'values' => [4, 4, 2, 3, 3, -1, null, 4]],
                ['name' => 'Southern Agricultural Growth Corridor', 'values' => [2, -2, 1, null, 2, -2, 1, null]],
                ['name' => 'National Rice Development Strategy', 'values' => [1, 3, null, 2, 1, null, -1, 3]],
                ['name' => 'National Water Policy (rev. 2022)', 'values' => [null, 2, 1, null, null, null, 2, null]],
            ],
        ];
    }

    /** Team home: workflow progress and outstanding work. */
    public static function teamProgress(): array
    {
        return [
            'steps' => [
                ['label' => 'Upload documents', 'state' => 'done', 'detail' => '5 documents, all processed'],
                ['label' => 'Run searches', 'state' => 'done', 'detail' => '581 automatic extracts'],
                ['label' => 'Verify extracts', 'state' => 'current', 'detail' => '212 of 581 reviewed (36%)'],
                ['label' => 'Score extracts', 'state' => 'current', 'detail' => '185 of 212 verified extracts scored'],
                ['label' => 'Write statements', 'state' => 'todo', 'detail' => '31 statements, 9 of 14 evidenced PAs covered'],
                ['label' => 'Select key findings', 'state' => 'todo', 'detail' => '0 selected'],
            ],
            'todo' => [
                ['count' => 369, 'label' => 'automatic extracts to review'],
                ['count' => 27, 'label' => 'verified extracts without a score'],
                ['count' => 5, 'label' => 'PAs with evidence but no statement'],
                ['count' => 0, 'label' => 'key findings selected for the summary'],
            ],
            'by_pa' => [
                ['pa' => '1.2', 'pending' => 212, 'unscored' => 9],
                ['pa' => '3.1', 'pending' => 48, 'unscored' => 6],
                ['pa' => '1.1', 'pending' => 41, 'unscored' => 4],
                ['pa' => '1.3', 'pending' => 33, 'unscored' => 3],
                ['pa' => '5.3', 'pending' => 21, 'unscored' => 5],
            ],
            'missing_statements' => ['3.3', '4.4', '5.1', '5.3', '1.4'],
            'activity' => [
                ['when' => '3 Nov', 'who' => 'A. Nakato', 'what' => 'verified 38 extracts under PA 1.2'],
                ['when' => '1 Nov', 'who' => 'J. Okello', 'what' => 'added 3 statements under PA 3.1'],
                ['when' => '28 Oct', 'who' => 'A. Nakato', 'what' => 'uploaded National Water Policy (rev. 2022)'],
            ],
        ];
    }

    /** Admin overview: all assessments. */
    public static function portfolio(): array
    {
        return [
            ['title' => 'Uganda 2025', 'country' => 'Uganda', 'status' => 'In Progress', 'progress' => 3, 'docs' => 5, 'pas' => 14, 'verified_pct' => 36, 'dist' => self::dist(3, 3, 0, 10, 14, 7, 3), 'median_year' => 2009, 'findings' => 0, 'last_activity' => '3 Nov 2026', 'days_idle' => 6],
            ['title' => 'Kenya 2025', 'country' => 'Kenya', 'status' => 'In Progress', 'progress' => 2, 'docs' => 7, 'pas' => 2, 'verified_pct' => 4, 'dist' => self::dist(0, 0, 1, 2, 1, 0, 0), 'median_year' => 2012, 'findings' => 0, 'last_activity' => '12 Aug 2026', 'days_idle' => 89],
            ['title' => 'Tanzania 2025', 'country' => 'Tanzania', 'status' => 'In Progress', 'progress' => 5, 'docs' => 6, 'pas' => 18, 'verified_pct' => 100, 'dist' => self::dist(1, 3, 2, 9, 16, 8, 2), 'median_year' => 2014, 'findings' => 6, 'last_activity' => '6 Nov 2026', 'days_idle' => 3],
            ['title' => 'Senegal 2025', 'country' => 'Senegal', 'status' => 'Review', 'progress' => 6, 'docs' => 8, 'pas' => 19, 'verified_pct' => 100, 'dist' => self::dist(2, 4, 1, 12, 18, 9, 5), 'median_year' => 2016, 'findings' => 5, 'last_activity' => '28 Oct 2026', 'days_idle' => 12, 'in_review_days' => 41],
            ['title' => 'Malawi 2025', 'country' => 'Malawi', 'status' => 'Review', 'progress' => 6, 'docs' => 4, 'pas' => 12, 'verified_pct' => 100, 'dist' => self::dist(0, 1, 1, 8, 9, 3, 1), 'median_year' => 2013, 'findings' => 4, 'last_activity' => '2 Nov 2026', 'days_idle' => 7, 'in_review_days' => 12],
            ['title' => 'Peru 2024', 'country' => 'Peru', 'status' => 'Finalised', 'progress' => 6, 'docs' => 9, 'pas' => 21, 'verified_pct' => 100, 'dist' => self::dist(5, 6, 2, 15, 22, 10, 6), 'median_year' => 2018, 'findings' => 6, 'last_activity' => '14 Feb 2026', 'days_idle' => 268],
            ['title' => 'Ghana 2024', 'country' => 'Ghana', 'status' => 'Finalised', 'progress' => 6, 'docs' => 6, 'pas' => 17, 'verified_pct' => 100, 'dist' => self::dist(1, 2, 0, 9, 15, 6, 2), 'median_year' => 2011, 'findings' => 4, 'last_activity' => '20 Jan 2026', 'days_idle' => 293],
            ['title' => 'Bolivia 2024', 'country' => 'Bolivia', 'status' => 'Dropped', 'progress' => 1, 'docs' => 2, 'pas' => 0, 'verified_pct' => 0, 'dist' => self::dist(0, 0, 0, 0, 0, 0, 0), 'median_year' => 2008, 'findings' => 0, 'last_activity' => '5 Jun 2025', 'days_idle' => 522],
        ];
    }

    public static function portfolioSignals(): array
    {
        return [
            'attention' => [
                ['label' => 'Stalled', 'detail' => 'Kenya 2025: no activity for 89 days', 'color' => 'danger'],
                ['label' => 'Ready for Review', 'detail' => 'Tanzania 2025: all extracts verified and 6 findings selected', 'color' => 'success'],
                ['label' => 'Awaiting peer review', 'detail' => 'Senegal 2025: in Review for 41 days', 'color' => 'warning'],
            ],
            'most_evidenced' => ['1.1', '1.2', '3.1'],
            'least_evidenced' => ['2.2', '4.3', '5.4'],
            'perverse_hotspots' => ['3.2', '1.2'],
            'search_terms' => [
                ['term' => 'agroecolog*', 'kept' => 82],
                ['term' => 'organic', 'kept' => 47],
                ['term' => 'diversif*', 'kept' => 39],
                ['term' => 'market*', 'kept' => 11],
            ],
        ];
    }
}
