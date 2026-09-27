<?php

declare(strict_types=1);

namespace App\Services;

class ExecutiveService
{
    private static ?array $executives = null;

    /**
     * Get the full list of SPS Executive Committee members.
     */
    public static function getExecutives(): array
    {
        if (self::$executives !== null) {
            return self::$executives;
        }

        self::$executives = [
            [
                'id' => 'exec_anik',
                'name_en' => 'Anik Kumar Saha',
                'name_bn' => 'অনিক কুমার সাহা',
                'designation_en' => 'President',
                'designation_bn' => 'সভাপতি',
                'adminship' => 'Super-Admin',
                'role_scope' => 'Everything (Full Platform & Institutional Control)',
                'role_scope_bn' => 'সমগ্র প্ল্যাটফর্ম ও প্রাতিষ্ঠানিক সর্বোচ্চ নিয়ন্ত্রণ',
                'rbac_role' => 'super_admin',
                'tier' => 'leadership',
                'photo' => 'assets/images/executives/anik-kumar-saha.png',
                'has_photo' => true,
            ],
            [
                'id' => 'exec_robin',
                'name_en' => 'Robin Dey',
                'name_bn' => 'রবিন দে',
                'designation_en' => 'General Secretary',
                'designation_bn' => 'সাধারণ সম্পাদক',
                'adminship' => 'Admin',
                'role_scope' => 'Access on literature section, publishing section, advertisement section',
                'role_scope_bn' => 'সাহিত্য, প্রকাশনা ও প্রচার বিভাগ নিয়ন্ত্রণ',
                'rbac_role' => 'admin',
                'tier' => 'leadership',
                'photo' => 'assets/images/executives/robin-dey.png',
                'has_photo' => true,
            ],
            [
                'id' => 'exec_likhon',
                'name_en' => 'Likhon Ghosh',
                'name_bn' => 'লিখন ঘোষ',
                'designation_en' => 'Organizing Secretary',
                'designation_bn' => 'সাংগঠনিক সম্পাদক',
                'adminship' => 'Admin',
                'role_scope' => 'Access on literature section, publishing section, advertisement section',
                'role_scope_bn' => 'সাহিত্য, প্রকাশনা ও সাংগঠনিক প্রচার বিভাগ নিয়ন্ত্রণ',
                'rbac_role' => 'admin',
                'tier' => 'leadership',
                'photo' => 'assets/images/executives/likhon-ghosh.jpg',
                'has_photo' => true,
            ],
            [
                'id' => 'exec_joy',
                'name_en' => 'Joy Chakraborty',
                'name_bn' => 'জয় চক্রবর্তী',
                'designation_en' => 'Treasurer',
                'designation_bn' => 'কোষাধ্যক্ষ',
                'adminship' => 'Finance-Admin',
                'role_scope' => 'Accounts & Financial Section (Maker-Checker & Sheets Sync)',
                'role_scope_bn' => 'হিসাব ও আর্থিক বিভাগ (মেকার-চেকার ও অডিট)',
                'rbac_role' => 'finance_officer',
                'tier' => 'leadership',
                'photo' => 'assets/images/executives/joy-chakraborty.png',
                'has_photo' => true,
            ],
            [
                'id' => 'exec_kallol',
                'name_en' => 'Roy Kallol',
                'name_bn' => 'রায় কল্লোল',
                'designation_en' => 'Office Secretary',
                'designation_bn' => 'দপ্তর সম্পাদক',
                'adminship' => 'Executive Member',
                'role_scope' => 'Secretarial & Office Administration',
                'role_scope_bn' => 'দাপ্তরিক নথি ও অভ্যন্তরীণ সমন্বয়',
                'rbac_role' => 'membership_officer',
                'tier' => 'secretariat',
                'photo' => null,
                'has_photo' => false,
            ],
            [
                'id' => 'exec_gourab',
                'name_en' => 'Gourab Chy',
                'name_bn' => 'গৌরব চৌধুরী',
                'designation_en' => 'Publicity Secretary 1',
                'designation_bn' => 'প্রচার সম্পাদক-১',
                'adminship' => 'Executive Member',
                'role_scope' => 'Public Relations & Outreach',
                'role_scope_bn' => 'প্রচার, গণযোগাযোগ ও অনুষ্ঠান প্রচার',
                'rbac_role' => 'moderator',
                'tier' => 'secretariat',
                'photo' => null,
                'has_photo' => false,
            ],
            [
                'id' => 'exec_ananda',
                'name_en' => 'Ananda Krishna Anu Das',
                'name_bn' => 'আনন্দ কৃষ্ণ অনু দাস',
                'designation_en' => 'Publicity Secretary 2',
                'designation_bn' => 'প্রচার সম্পাদক-২',
                'adminship' => 'Executive Member',
                'role_scope' => 'Media Campaigns & Community Outreach',
                'role_scope_bn' => 'ডিজিটাল প্রচারণা ও সম্প্রদায় সংযোগ',
                'rbac_role' => 'moderator',
                'tier' => 'secretariat',
                'photo' => null,
                'has_photo' => false,
            ],
            [
                'id' => 'exec_anita',
                'name_en' => 'Ani Ta',
                'name_bn' => 'অনিতা',
                'designation_en' => 'Cultural Secretary 1',
                'designation_bn' => 'সাংস্কৃতিক সম্পাদক-১',
                'adminship' => 'Executive Member',
                'role_scope' => 'Cultural Programs & Heritage Affairs',
                'role_scope_bn' => 'ঐতিহ্যবাহী সাংস্কৃতিক অনুষ্ঠান ও উৎসব',
                'rbac_role' => 'project_manager',
                'tier' => 'secretariat',
                'photo' => null,
                'has_photo' => false,
            ],
            [
                'id' => 'exec_pushpita',
                'name_en' => 'Pushpita Roy',
                'name_bn' => 'পুষ্পিতা রায়',
                'designation_en' => 'Cultural Secretary 2',
                'designation_bn' => 'সাংস্কৃতিক সম্পাদক-২',
                'adminship' => 'Executive Member',
                'role_scope' => 'Cultural Programs & Fine Arts',
                'role_scope_bn' => 'সাংস্কৃতিক পরিবেশনা ও কলা ঐতিহ্য',
                'rbac_role' => 'project_manager',
                'tier' => 'secretariat',
                'photo' => null,
                'has_photo' => false,
            ],
            [
                'id' => 'exec_pranto',
                'name_en' => 'Pranto Saha',
                'name_bn' => 'প্রান্ত সাহা',
                'designation_en' => 'Scripture Affairs Secretary 1',
                'designation_bn' => 'শাস্ত্র বিষয়ক সম্পাদক-১',
                'adminship' => 'Literature-Admin',
                'role_scope' => 'Access on literature section, scriptures & theological texts',
                'role_scope_bn' => 'শাস্ত্রীয় জ্ঞানপীঠ ও সাহিত্য বিভাগ নিয়ন্ত্রণ',
                'rbac_role' => 'content_editor',
                'tier' => 'scholarly',
                'photo' => null,
                'has_photo' => false,
            ],
            [
                'id' => 'exec_goutam',
                'name_en' => 'Goutam Dandapat',
                'name_bn' => 'গৌতম দণ্ডপাট',
                'designation_en' => 'Scripture Affairs Secretary 2',
                'designation_bn' => 'শাস্ত্র বিষয়ক সম্পাদক-২',
                'adminship' => 'Literature-Admin',
                'role_scope' => 'Access on literature section, scriptures & theological texts',
                'role_scope_bn' => 'শাস্ত্রীয় শ্লোক, বঙ্গানুবাদ ও প্রামাণ্য শাস্ত্র বিভাগ',
                'rbac_role' => 'content_editor',
                'tier' => 'scholarly',
                'photo' => null,
                'has_photo' => false,
            ],
            [
                'id' => 'exec_rahul',
                'name_en' => 'Rahul Kumar Sutradhar',
                'name_bn' => 'রাহুল কুমার সূত্রধর',
                'designation_en' => 'Scripture Affairs Secretary 3',
                'designation_bn' => 'শাস্ত্র বিষয়ক সম্পাদক-৩',
                'adminship' => 'Literature-Admin',
                'role_scope' => 'Access on literature section, scriptures & theological texts',
                'role_scope_bn' => 'শাস্ত্র সংকলন ও গবেষণা পর্যালোচনা',
                'rbac_role' => 'content_editor',
                'tier' => 'scholarly',
                'photo' => null,
                'has_photo' => false,
            ],
            [
                'id' => 'exec_sanjoy',
                'name_en' => 'Sanjoy Chandra Das',
                'name_bn' => 'সঞ্জয় চন্দ্র দাস',
                'designation_en' => 'Education Affairs Secretary',
                'designation_bn' => 'শিক্ষা বিষয়ক সম্পাদক',
                'adminship' => 'Executive Member',
                'role_scope' => 'Educational Seminars & Youth Philosophy Programs',
                'role_scope_bn' => 'ধর্মীয় ও দার্শনিক শিক্ষা কার্যক্রম',
                'rbac_role' => 'volunteer_coordinator',
                'tier' => 'secretariat',
                'photo' => null,
                'has_photo' => false,
            ],
            [
                'id' => 'exec_shyamoli',
                'name_en' => 'Shyamoli Das',
                'name_bn' => 'শ্যামলী দাস',
                'designation_en' => "Women's Affairs Secretary",
                'designation_bn' => 'নারী বিষয়ক সম্পাদক',
                'adminship' => 'Executive Member',
                'role_scope' => "Women's Empowerment & Community Participation",
                'role_scope_bn' => 'নারীদের নেতৃত্ব বিকাশ ও সমাজকল্যাণ',
                'rbac_role' => 'membership_officer',
                'tier' => 'secretariat',
                'photo' => null,
                'has_photo' => false,
            ],
            [
                'id' => 'exec_ankan',
                'name_en' => 'Sree Ankan Bhattacharjee',
                'name_bn' => 'শ্রী অঙ্কন ভট্টাচার্য',
                'designation_en' => 'Expatriate Affairs Secretary',
                'designation_bn' => 'প্রবাসী কল্যাণ সম্পাদক',
                'adminship' => 'Executive Member',
                'role_scope' => 'Global Community & Expatriate Coordination',
                'role_scope_bn' => 'আন্তর্জাতিক ও প্রবাসী শুভানুধ্যায়ী সমন্বয়',
                'rbac_role' => 'volunteer_coordinator',
                'tier' => 'secretariat',
                'photo' => null,
                'has_photo' => false,
            ],
            [
                'id' => 'exec_badhan',
                'name_en' => 'Badhan Roy',
                'name_bn' => 'বাঁধন রায়',
                'designation_en' => 'Publication Secretary 1',
                'designation_bn' => 'প্রকাশনা সম্পাদক-১',
                'adminship' => 'Publishing-Admin',
                'role_scope' => 'Access on publishing section, digital editions & printed periodicals',
                'role_scope_bn' => 'গ্রন্থাগার, ই-বুক প্রকাশনা ও মুদ্রিত পত্রিকা বিভাগ',
                'rbac_role' => 'library_manager',
                'tier' => 'publishing',
                'photo' => 'assets/images/executives/badhan-roy.jpg',
                'has_photo' => true,
            ],
            [
                'id' => 'exec_partha',
                'name_en' => 'Partha Pratim Dutta',
                'name_bn' => 'পার্থ প্রতিম দত্ত',
                'designation_en' => 'Publication Secretary 2',
                'designation_bn' => 'প্রকাশনা সম্পাদক-২',
                'adminship' => 'Publishing-Admin',
                'role_scope' => 'Access on publishing section, digital editions & printed periodicals',
                'role_scope_bn' => 'প্রকাশনা মুদ্রণ, ক্যাটালগ ও ডিজিটাল আর্কাইভ পরিচালনা',
                'rbac_role' => 'library_manager',
                'tier' => 'publishing',
                'photo' => 'assets/images/executives/partha-pratim-dutta.png',
                'has_photo' => true,
            ],
        ];

        return self::$executives;
    }

    /**
     * Get executives grouped by tier (Leadership, Scholarly, Publishing, Secretariat).
     */
    public static function getExecutivesGrouped(): array
    {
        $all = self::getExecutives();
        $grouped = [
            'leadership' => [],
            'scholarly' => [],
            'publishing' => [],
            'secretariat' => []
        ];

        foreach ($all as $exec) {
            $tier = $exec['tier'] ?? 'secretariat';
            $grouped[$tier][] = $exec;
        }

        return $grouped;
    }
}
