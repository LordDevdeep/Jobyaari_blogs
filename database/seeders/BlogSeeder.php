<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        $uploadDir = public_path('uploads/blogs');
        if (! File::exists($uploadDir)) {
            File::makeDirectory($uploadDir, 0755, true);
        }

        $bySlug = Category::pluck('id', 'slug');

        $blogs = $this->blogData();

        foreach ($blogs as $i => $data) {
            $categoryId = $bySlug[$data['category_slug']] ?? Category::first()->id;
            $slug = Blog::generateUniqueSlug($data['title']);
            $imageName = $this->fetchImage($i + 1, $slug, $uploadDir);

            Blog::create([
                'title' => $data['title'],
                'slug' => $slug,
                'short_description' => $data['short_description'],
                'content' => $data['content'],
                'image' => $imageName,
                'category_id' => $categoryId,
                'views' => random_int(50, 8500),
                'published_at' => now()->subDays(random_int(0, 75))->subHours(random_int(0, 23)),
            ]);
        }
    }

    private function fetchImage(int $seed, string $slug, string $dir): ?string
    {
        $filename = $slug . '.jpg';
        $path = $dir . DIRECTORY_SEPARATOR . $filename;
        try {
            $resp = Http::timeout(15)->retry(2, 500)->get("https://picsum.photos/seed/jy{$seed}/800/450");
            if ($resp->successful()) {
                File::put($path, $resp->body());
                return $filename;
            }
        } catch (\Throwable $e) {
            // fall through to null
        }
        return null;
    }

    /** @return array<int,array{title:string,short_description:string,content:string,category_slug:string}> */
    private function blogData(): array
    {
        $longBody = function (string $intro, array $bullets, string $tableTitle, array $rows): string {
            $html = "<p>{$intro}</p>";
            $html .= '<h2>Key Highlights</h2><ul>';
            foreach ($bullets as $b) {
                $html .= "<li>{$b}</li>";
            }
            $html .= '</ul>';
            $html .= "<h2>{$tableTitle}</h2>";
            $html .= '<table border="1" cellpadding="6" cellspacing="0" style="border-collapse:collapse;width:100%"><thead><tr><th>Detail</th><th>Information</th></tr></thead><tbody>';
            foreach ($rows as $r) {
                $html .= "<tr><td><strong>{$r[0]}</strong></td><td>{$r[1]}</td></tr>";
            }
            $html .= '</tbody></table>';
            $html .= '<h3>How to Apply</h3><p>Candidates are advised to read the full notification before applying. Visit the official website, register with valid email and mobile number, fill the application form carefully, upload required documents, pay the fee online, and submit. Take a printout of the confirmation page for future reference.</p>';
            $html .= '<h3>Important Instructions</h3><ol><li>Use a recent passport-size photograph as per specifications.</li><li>Cross-check personal details before final submission.</li><li>Keep the application number safe — it is required for downloads later.</li><li>Bookmark the official portal and follow JobYaari for updates.</li></ol>';
            return $html;
        };

        return [
            // Admit Card x 4
            [
                'category_slug' => 'admit-card',
                'title' => 'SSC CGL 2026 Tier-I Admit Card Released — Download Now',
                'short_description' => 'Staff Selection Commission has released the SSC CGL 2026 Tier-I admit card. Download your e-call letter using registration number and date of birth from the official portal.',
                'content' => $longBody(
                    'The Staff Selection Commission (SSC) has officially released the admit cards for the Combined Graduate Level (CGL) 2026 Tier-I examination. Candidates appearing for the exam can now download their hall ticket from the regional SSC websites.',
                    ['Exam mode: Computer Based Test (CBT)', 'Total duration: 60 minutes', 'Sections: General Intelligence, GA, Quant, English', 'Negative marking: 0.50 per wrong answer'],
                    'Exam Schedule',
                    [['Event', 'Date'], ['Admit Card Release', '08 May 2026'], ['Tier-I Exam Window', '20 May – 28 May 2026'], ['Result (Tentative)', 'July 2026']]
                ),
            ],
            [
                'category_slug' => 'admit-card',
                'title' => 'UPSC Civil Services Prelims 2026 Admit Card Out',
                'short_description' => 'UPSC has uploaded e-admit cards for the Civil Services Preliminary Examination 2026. Candidates must carry a printout along with valid photo ID to the exam centre.',
                'content' => $longBody(
                    'The Union Public Service Commission (UPSC) has released the e-Admit Card for the Civil Services (Preliminary) Examination 2026 on its official website upsc.gov.in. The examination is scheduled to be conducted across India in two sessions on the notified date.',
                    ['Two papers: GS Paper-I and CSAT', 'Paper duration: 2 hours each', 'CSAT is qualifying — 33% required', 'Photo ID mandatory at centre'],
                    'Important Dates',
                    [['Notification', 'February 2026'], ['Application Window', 'Feb – Mar 2026'], ['Admit Card Release', 'May 2026'], ['Examination Date', '26 May 2026']]
                ),
            ],
            [
                'category_slug' => 'admit-card',
                'title' => 'IBPS PO Mains 2026 Admit Card Available for Download',
                'short_description' => 'IBPS has released the admit cards for the Probationary Officer Main Examination 2026. The exam will assess Reasoning & Computer Aptitude, English, Data Analysis and General Awareness.',
                'content' => $longBody(
                    'Institute of Banking Personnel Selection (IBPS) has activated the admit card download link for PO/MT Mains 2026. The objective and descriptive paper will be held on the scheduled date in a single session.',
                    ['Objective: 155 questions / 200 marks', 'Descriptive: Essay + Letter / 25 marks', 'Total duration: 3 hours 30 mins', 'Sectional timing applicable'],
                    'Exam Pattern',
                    [['Section', 'Marks'], ['Reasoning & Computer', '60'], ['English Language', '40'], ['Data Analysis', '60'], ['General/Economy/Banking', '40']]
                ),
            ],
            [
                'category_slug' => 'admit-card',
                'title' => 'RRB NTPC CBT-2 Admit Card 2026 Released Region-wise',
                'short_description' => 'Railway Recruitment Board has released the CBT-2 admit card for NTPC Graduate posts. Aspirants can download e-call letter four days before the exam date.',
                'content' => $longBody(
                    'Railway Recruitment Boards (RRBs) across India have released the CBT-2 admit cards for Non-Technical Popular Categories (NTPC) Graduate posts. Candidates are advised to download the admit card from their respective RRB regional websites.',
                    ['Duration: 90 minutes (120 questions)', 'Negative marking: 1/3 per wrong answer', 'Sections: GA, Maths, GI & Reasoning', 'Normalisation applied across shifts'],
                    'Key Information',
                    [['Posts Covered', 'CA, JAA, SCRA, Goods Guard, Station Master'], ['Mode', 'Online CBT'], ['CBT-2 Window', '12 May – 22 May 2026'], ['Tie-Breaker', 'Age based']]
                ),
            ],

            // Latest Jobs x 4
            [
                'category_slug' => 'latest-jobs',
                'title' => 'SBI Clerk Recruitment 2026 — 8000+ Junior Associate Vacancies Notified',
                'short_description' => 'State Bank of India invites online applications for Junior Associate (Clerk) posts in clerical cadre. Graduates aged 20 to 28 years can apply through sbi.co.in.',
                'content' => $longBody(
                    'State Bank of India (SBI) has released the official notification for the recruitment of 8,000+ Junior Associates (Customer Support & Sales) in clerical cadre for the year 2026. Eligible Indian citizens may apply through the SBI careers portal.',
                    ['Total vacancies: 8,283 (tentative)', 'Eligibility: Graduate in any discipline', 'Age: 20–28 years (relaxation as per norms)', 'Selection: Prelims + Mains + LPT'],
                    'Application Schedule',
                    [['Notification Date', '02 May 2026'], ['Online Application Start', '06 May 2026'], ['Last Date', '26 May 2026'], ['Prelims (Tentative)', 'June 2026']]
                ),
            ],
            [
                'category_slug' => 'latest-jobs',
                'title' => 'IBPS RRB Officer Scale-I & Office Assistant 2026 — Apply Online',
                'short_description' => 'IBPS has notified vacancies for Officer Scale-I and Office Assistant (Multipurpose) in Regional Rural Banks. Online registration is open till the last date.',
                'content' => $longBody(
                    'Institute of Banking Personnel Selection (IBPS) has invited online applications from eligible Indian citizens for the post of Officer Scale-I (PO) and Office Assistant (Multipurpose) in Regional Rural Banks (RRBs) across the country.',
                    ['Application fee: Rs. 175 (SC/ST/PWD) / Rs. 850 (others)', 'Selection: Prelims + Mains + Interview (Scale-I)', 'Office Assistant: Prelims + Mains only', 'Service across 43 RRBs in India'],
                    'Important Dates',
                    [['Apply Online From', '01 May 2026'], ['Apply Online Till', '21 May 2026'], ['Prelims (Officer)', 'August 2026'], ['Mains (Tentative)', 'September 2026']]
                ),
            ],
            [
                'category_slug' => 'latest-jobs',
                'title' => 'Indian Army Agniveer Recruitment 2026 Notification Out',
                'short_description' => 'Indian Army has released the Agniveer recruitment notification 2026. Unmarried male and female candidates aged 17.5 to 21 years can apply online at joinindianarmy.nic.in.',
                'content' => $longBody(
                    'The Indian Army has released the official notification for Agniveer recruitment 2026 under the Agnipath Scheme. Eligible candidates can apply online for various categories including General Duty (GD), Technical, Clerk/Store Keeper Technical, and Tradesmen.',
                    ['Tenure: 4 years (with 25% retention option)', 'Service across all arms and services', 'Online CEE before physical tests', 'Skill grants offered post tenure'],
                    'Categories & Eligibility',
                    [['Agniveer GD', '10th pass / 45% aggregate'], ['Agniveer Technical', '10+2 PCM + 50%'], ['Agniveer Clerk', '10+2 with 60%'], ['Agniveer Tradesman', '10th pass']]
                ),
            ],
            [
                'category_slug' => 'latest-jobs',
                'title' => 'UPSC NDA & NA (II) 2026 — 400 Vacancies for 10+2 Pass',
                'short_description' => 'UPSC has issued the National Defence Academy & Naval Academy Examination (II) 2026 notification. Unmarried male and female candidates may apply online for entry into Army, Navy and Air Force.',
                'content' => $longBody(
                    'The Union Public Service Commission has released the notification for National Defence Academy and Naval Academy Examination (II) 2026. The examination is conducted twice a year for entry into the prestigious tri-services academies of India.',
                    ['Eligibility: 10+2 (PCM for Air Force & Navy)', 'Age: 16.5 to 19.5 years', 'Selection: Written + SSB Interview', 'Stipend during training as per norms'],
                    'Exam Pattern',
                    [['Paper-I — Maths', '300 marks'], ['Paper-II — GAT', '600 marks'], ['SSB Interview', '900 marks'], ['Total', '1800 marks']]
                ),
            ],

            // Results x 4
            [
                'category_slug' => 'results',
                'title' => 'UPSC CSE 2025 Final Result Declared — Topper List Released',
                'short_description' => 'The Union Public Service Commission has announced the Civil Services Examination 2025 final result. Roll numbers of successful candidates have been published on upsc.gov.in.',
                'content' => $longBody(
                    'The Union Public Service Commission has declared the final result of the Civil Services Examination 2025. A total of 1,016 candidates have been recommended for appointment to various Group A and Group B Central services including IAS, IPS, IFS and allied services.',
                    ['Total recommended: 1,016', 'IAS posts: 180', 'IPS posts: 200', 'IFS posts: 38'],
                    'Top Rankers (Indicative)',
                    [['Rank 1', 'Notified on website'], ['Rank 2', 'Notified on website'], ['Rank 3', 'Notified on website'], ['Reserve List', 'Published separately']]
                ),
            ],
            [
                'category_slug' => 'results',
                'title' => 'SSC CHSL 2025 Tier-I Result Out — Check Cut-off & Marks',
                'short_description' => 'Staff Selection Commission has uploaded the SSC CHSL 2025 Tier-I result. Candidates can check their qualifying status and category-wise cut-off marks on ssc.gov.in.',
                'content' => $longBody(
                    'The Staff Selection Commission (SSC) has declared the result for the Combined Higher Secondary Level (10+2) Examination 2025 Tier-I. Candidates who appeared can check their qualifying status and download the marks statement.',
                    ['Total qualified for Tier-II: 35,000+', 'Result format: PDF with Roll No.', 'Cut-off declared category-wise', 'Tier-II scheduled in two months'],
                    'Cut-off Highlights',
                    [['UR', '156.50'], ['OBC', '149.25'], ['SC', '138.75'], ['ST', '132.50'], ['EWS', '152.25']]
                ),
            ],
            [
                'category_slug' => 'results',
                'title' => 'IBPS Clerk Mains 2025 Result Declared — Provisional Allotment Soon',
                'short_description' => 'IBPS has announced the Clerk Mains 2025 result on its official website. Selected candidates will be allotted participating banks based on preferences and category.',
                'content' => $longBody(
                    'Institute of Banking Personnel Selection (IBPS) has declared the result of the Common Recruitment Process for Clerks (CRP Clerks-XIV) Mains examination. Provisional allotment to participating public sector banks will follow shortly.',
                    ['Status: Qualifying / Not Qualifying', 'Marks statement available for 30 days', 'No interview round for Clerk cadre', 'Joining subject to document verification'],
                    'Allotment Process',
                    [['Step 1', 'Cut-off finalised'], ['Step 2', 'Bank-wise allocation'], ['Step 3', 'Allotment letter'], ['Step 4', 'Pre-joining formalities']]
                ),
            ],
            [
                'category_slug' => 'results',
                'title' => 'RRB Group D 2025 Final Result Published — Document Verification Begins',
                'short_description' => 'Railway Recruitment Board has released the Group D 2025 final result. Shortlisted candidates have been called for document verification and medical examination.',
                'content' => $longBody(
                    'The Railway Recruitment Boards (RRBs) have published the final result of Group D Recruitment 2025. Selected candidates have been shortlisted for the next stage of document verification (DV) and medical examination (ME).',
                    ['Posts covered: Track Maintainer, Helper, Porter, etc.', 'Pay Level: 1 (7th CPC)', 'DV location: As per RRB allotment', 'Medical: Vision standards as notified'],
                    'Next Steps',
                    [['Stage', 'Activity'], ['DV', 'Original document checking'], ['ME', 'Vision & physical fitness'], ['Final Panel', 'After DV + ME'], ['Posting', 'As per zonal vacancies']]
                ),
            ],

            // Answer Key x 4
            [
                'category_slug' => 'answer-key',
                'title' => 'SSC MTS 2026 Answer Key Released — Raise Objections by 10 May',
                'short_description' => 'SSC has uploaded the tentative answer key for Multi-Tasking Staff and Havaldar examination 2026. Candidates can challenge answers online with prescribed fee.',
                'content' => $longBody(
                    'The Staff Selection Commission (SSC) has released the tentative answer key for the Multi-Tasking (Non-Technical) Staff and Havaldar Examination 2026 Computer Based Test. Candidates can access their response sheet and the tentative key.',
                    ['Objection fee: Rs. 100 per question', 'Window: 3 days from release', 'Final key after expert review', 'Final key is final and binding'],
                    'Objection Process',
                    [['Step', 'Action'], ['1', 'Login with Roll & Password'], ['2', 'Click "Submit Representation"'], ['3', 'Select question & upload proof'], ['4', 'Pay fee and submit']]
                ),
            ],
            [
                'category_slug' => 'answer-key',
                'title' => 'UPSC Prelims 2025 Answer Key with Detailed Solutions',
                'short_description' => 'Following the conclusion of UPSC Civil Services Prelims 2025, leading institutes have released subject-wise answer keys with detailed explanations for GS Paper-I and CSAT.',
                'content' => $longBody(
                    'The Civil Services Preliminary Examination 2025 was conducted by UPSC across the country. While UPSC itself releases the official answer key after the final result, several reputed coaching institutes have published their analyses and tentative keys.',
                    ['GS Paper-I: 100 questions / 200 marks', 'CSAT: 80 questions / 200 marks', 'Tentative cut-off: 95–100 (UR)', 'Final key released post final result'],
                    'Difficulty Analysis',
                    [['Polity', 'Moderate'], ['Geography', 'Moderate to Tough'], ['Economy', 'Tough'], ['Environment', 'Moderate'], ['Current Affairs', 'Conceptual']]
                ),
            ],
            [
                'category_slug' => 'answer-key',
                'title' => 'IBPS RRB Clerk Prelims 2026 Answer Key & Response Sheet',
                'short_description' => 'IBPS has activated the response sheet and tentative answer key links for RRB Office Assistant Prelims 2026. Candidates can check question-wise responses.',
                'content' => $longBody(
                    'Institute of Banking Personnel Selection (IBPS) has uploaded the response sheets and tentative answer keys for RRB Office Assistant (Multipurpose) Preliminary Examination 2026. Candidates can review their attempted questions and compare with the key.',
                    ['Sections: Reasoning + Numerical Ability', 'Duration: 45 minutes', 'Negative marking: 0.25 per wrong', 'Cut-off varies by state'],
                    'Tentative Cut-off (State-wise)',
                    [['Uttar Pradesh', '76–79'], ['Bihar', '72–75'], ['Madhya Pradesh', '70–74'], ['Maharashtra', '68–71'], ['Karnataka', '65–69']]
                ),
            ],
            [
                'category_slug' => 'answer-key',
                'title' => 'RRB ALP Stage-I Answer Key 2026 — Objection Window Open',
                'short_description' => 'Railway Recruitment Boards have released the Assistant Loco Pilot Stage-I answer key. Candidates may raise objections by paying the prescribed fee per question.',
                'content' => $longBody(
                    'Railway Recruitment Boards (RRBs) across regions have uploaded the tentative answer key, question paper and response sheet for the Assistant Loco Pilot (ALP) Stage-I CBT 2026. The objection window is open for a limited period.',
                    ['Objection fee: Rs. 50 per question', 'Refund only for valid objections', 'Login with Reg. No. and DOB', 'Final key after expert committee review'],
                    'Stage-I Pattern Recap',
                    [['Section', 'Questions'], ['Maths', '20'], ['General Intelligence', '25'], ['General Science', '20'], ['General Awareness & CA', '10']]
                ),
            ],

            // Syllabus x 4
            [
                'category_slug' => 'syllabus',
                'title' => 'SSC CGL 2026 Tier-II Syllabus & New Exam Pattern Explained',
                'short_description' => 'A detailed breakdown of the SSC CGL 2026 Tier-II syllabus, section-wise weightage and the latest exam pattern as per the official commission notification.',
                'content' => $longBody(
                    'The Staff Selection Commission (SSC) has notified the revised pattern for CGL Tier-II from the 2026 cycle. Tier-II consists of three papers — Paper-I (compulsory for all), Paper-II (JSO), and Paper-III (AAO/Assistant Accountant).',
                    ['Paper-I: Quant + Reasoning + English + GA + Computer + DI', 'Paper-II: Statistics (only for JSO)', 'Paper-III: General Studies — Finance & Economics', 'Skill test qualifying for some posts'],
                    'Paper-I Sections',
                    [['Section I', 'Quant + Reasoning'], ['Section II', 'English + GA'], ['Section III', 'Computer Knowledge'], ['Section IV (DEST)', 'Data Entry Skill Test']]
                ),
            ],
            [
                'category_slug' => 'syllabus',
                'title' => 'UPSC Mains 2026 Syllabus — General Studies and Optional Subjects',
                'short_description' => 'A complete reference of the UPSC Civil Services Mains 2026 syllabus including the four GS papers, Essay, optional subject list, and language papers.',
                'content' => $longBody(
                    'The Union Public Service Commission Mains examination is a written test comprising nine papers, of which two are qualifying (English and Indian Language). The other seven papers determine the final rank along with the Personality Test (Interview).',
                    ['Paper-A: Indian Language (qualifying)', 'Paper-B: English (qualifying)', 'Paper-I: Essay', 'Papers II–V: GS-I to GS-IV', 'Papers VI–VII: Optional Subject'],
                    'GS Paper Themes',
                    [['GS-I', 'Indian Heritage, History, Geography, Society'], ['GS-II', 'Polity, Governance, IR'], ['GS-III', 'Economy, Environment, Internal Security'], ['GS-IV', 'Ethics, Integrity & Aptitude']]
                ),
            ],
            [
                'category_slug' => 'syllabus',
                'title' => 'IBPS PO 2026 Prelims & Mains Syllabus — Complete Guide',
                'short_description' => 'Detailed syllabus and topic-wise weightage for IBPS PO Prelims and Mains examination 2026 including the descriptive paper for Essay and Letter Writing.',
                'content' => $longBody(
                    'The IBPS PO selection process includes Preliminary Examination, Main Examination, and Personal Interview. Candidates qualifying the Mains and Interview are selected for the Common Recruitment Process for Probationary Officer / Management Trainee posts.',
                    ['Prelims: 3 sections (English, Quant, Reasoning)', 'Mains: 4 sections + Descriptive', 'Sectional + overall cut-off applicable', 'Final merit: Mains (80) + Interview (20)'],
                    'Mains Syllabus Snapshot',
                    [['Reasoning & Computer', 'Coding, Puzzles, Networking, MS Office'], ['English', 'RC, Cloze, Errors, Para Jumbles'], ['Data Analysis', 'DI, Caselet, Quadratic Eqns, Probability'], ['GA/Banking', 'Current Affairs, Banking, Economy']]
                ),
            ],
            [
                'category_slug' => 'syllabus',
                'title' => 'RRB NTPC 2026 Syllabus & Exam Pattern (CBT-I + CBT-II)',
                'short_description' => 'A consolidated reference of the RRB NTPC 2026 syllabus for both CBT-I and CBT-II stages along with the latest examination pattern and section-wise marks.',
                'content' => $longBody(
                    'The Railway Recruitment Board NTPC 2026 selection process involves two computer-based tests followed by typing skill test or computer-based aptitude test (for specified posts) and document verification.',
                    ['CBT-I: 100 questions / 90 minutes', 'CBT-II: 120 questions / 90 minutes', 'Sections: Maths, GI & Reasoning, GA & CA', 'Skill test: Stenographer / Typing'],
                    'Syllabus Snapshot',
                    [['Maths', 'Arithmetic, Algebra, Geometry, Mensuration'], ['Reasoning', 'Analogy, Series, Statement & Conclusion'], ['GA & CA', 'History, Polity, Geography, Sports, Awards'], ['Computer Basics', 'For typing/aptitude test posts']]
                ),
            ],
        ];
    }
}
