<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProposalSection;
use App\Models\ProposalSectionColumn;
use App\Models\ProposalSectionRow;
use App\Models\ProposalSimpleOption;

class ProposalTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $mobile = ProposalSection::firstOrCreate(
            ['key'=>'mobile_env'],
            ['title'=>'Mobile System Environment','type'=>'complex','sort_order'=>1,'is_active'=>true]
        );
        $mobileCols = [
            ['key'=>'platform','label'=>'Platform'],
            ['key'=>'display_screen','label'=>'Display Screen'],
            ['key'=>'os_support','label'=>'OS Support'],
            ['key'=>'engine','label'=>'Engine','input_type'=>'chips'],
            ['key'=>'programming_language','label'=>'Programming Language','input_type'=>'chips'],
        ];
        foreach ($mobileCols as $i=>$c){
            ProposalSectionColumn::firstOrCreate(['section_id'=>$mobile->id,'key'=>$c['key']],['label'=>$c['label'],'input_type'=>$c['input_type'] ?? 'text','sort_order'=>$i]);
        }
        ProposalSectionRow::firstOrCreate(['section_id'=>$mobile->id,'sort_order'=>1],[
            'values'=>[
                'platform'=>'Android - Phone',
                'display_screen'=>'Locked Portrait 5.5" - 6.8"',
                'os_support'=>'Android 6.0 - Latest*',
                'engine'=>['Android Native (Android Studio)','Flutter','React Native'],
                'programming_language'=>['Kotlin','Dart','JavaScript']
            ]
        ]);

        $web = ProposalSection::firstOrCreate(
            ['key'=>'web_env'],
            ['title'=>'Web System Environment','type'=>'complex','sort_order'=>2,'is_active'=>true]
        );
        $webCols = [
            ['key'=>'platform','label'=>'Platform'],
            ['key'=>'display_screen','label'=>'Display Screen'],
            ['key'=>'browser_support','label'=>'Browser Support'],
            ['key'=>'framework','label'=>'Framework','input_type'=>'chips'],
            ['key'=>'programming_language','label'=>'Programming Language','input_type'=>'chips'],
        ];
        foreach ($webCols as $i=>$c){
            ProposalSectionColumn::firstOrCreate(['section_id'=>$web->id,'key'=>$c['key']],['label'=>$c['label'],'input_type'=>$c['input_type'] ?? 'text','sort_order'=>$i]);
        }
        ProposalSectionRow::firstOrCreate(['section_id'=>$web->id,'sort_order'=>1],[
            'values'=>[
                'platform'=>'Website - Frontend Interface',
                'display_screen'=>'Resolution 1920px x 1080px, Responsive Mobile View',
                'browser_support'=>'Optimal on Google Chrome & Firefox',
                'framework'=>['Laravel','Spring','ReactJS'],
                'programming_language'=>['Php','Java','JavaScript']
            ]
        ]);

        $simpleSeeds = [
            'frontend_lang'=>['Bahasa Indonesia','English','Other (...)'],
            'app_info'=>['Publik Apps/Website','Internal Apps/Website'],
            'account_availability'=>['Account Playstore & Appstore disediakan oleh (nama klien)','Account Playstore & Appstore disediakan oleh Crocodic'],
            'db_availability'=>['Server disediakan oleh (nama klien)','Server disediakan oleh Crocodic'],
            'db_info'=>['MySQL','SQL Server','Postgre'],
        ];
        foreach ($simpleSeeds as $key=>$labels){
            foreach ($labels as $i=>$label){
                ProposalSimpleOption::firstOrCreate(['section_key'=>$key,'label'=>$label],[ 'sort_order'=>$i,'is_active'=>true,'is_other'=>str_contains($label,'Other') ]);
            }
        }
    }
}

