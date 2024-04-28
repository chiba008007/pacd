<?php
namespace App\Library;

use App\Models\Page;
use App\Models\PageContent;
use App\Models\PageSubContent;
use App\Models\FormInput;
use App\Models\Event;
use App\Models\Event_join;

class PagesLibrary {

    /**
     * 公開ページに必要なデータを配列で返す
     *
     * @param array $where
     * @return array
     */
    static public function getContents($where,$event_id=0) {
        $page = Page::where($where)->first();
        if ($page) {
            $contents = PageContent::where('page_id', $page->id)->orderByRaw('display_order IS NULL ASC')->orderBy('display_order')->get()->toArray();
            foreach($contents as $key => $section) {
                // セクションコンテンツがテーブルまたはリストの場合、別テーブルからデータを取得
                if (array_search($section['content_type'], ['table', 'list']) !== false) {
                    $query =
                        PageSubContent::where('page_content_id', $section['id'])
                                        ->where(function($query){
                                            return $query
                                                ->orWhere('content1' , '!=', '')
                                                ->orWhere('content2', '!=', '');
                                        });

                    if($page->route_name === "top"){
                        $query = $query->orderBy('content1','DESC');
                        $query = $query->get()->toArray();

                        //トップページの並び順を変更
                        $news = [];
                         foreach($query as $keys=>$value){
                            if(
                                preg_match("/日/",$value[ 'content1' ]) &&
                                preg_match("/年/",$value[ 'content1' ]) &&
                                preg_match("/月/",$value[ 'content1' ])
                            ){
                                $event_date = $value[ 'content1' ];
                                $event_date = str_replace('日', '', $event_date);
                                $event_date = str_replace('年', '-', $event_date);
                                $event_date = str_replace('月', '-', $event_date);
                                $ex = explode("-",strip_tags($event_date));
                                if(count($ex) > 0 ){
                                    $news[$keys] = sprintf("%04d%02d%02d",$ex[0],$ex[1],$ex[2]);
                                }
                            }
                        }
                        if(count($news) > 0 ){
                            @array_multisort( $news, SORT_DESC, SORT_NUMERIC, $query);
                        }
                    }else{
                        $query = $query->get()->toArray();
                    }
                    $contents[$key]['sub_contents'] = $query;
                }
            }
            $data = $page->toArray();
            $data['contents'] = $contents;

            if ($page->route_name === 'top' || $page->route_name === 'eventlist' || $page->route_name === 'kyosan') {
                // 次回イベント情報
                $events = Event::getEventLists();
                $data['events'] = $events;
                //参加料金
                $eventsjoin = Event_join::getEventJoin();
                $data['eventsjoin'] = $eventsjoin;
            } elseif (in_array($page->route_name, ['schedule', 'reikai.history', 'touronkai.history', 'kosyukai.history'])) {
                if ($page->route_name == 'schedule') {
                    // 開催行事一覧
                    $data['is_itiran_page_list'] = true;
                    $data['events'] = Event::getEventListsSchedule();
                } else {
                    // 過去のイベント一覧
                    $data['is_itiran_page'] = true;
                    $prefix = strstr($page->route_name, '.', true);
                    $data['events'] = Event::getEventListsCategory(config("pacd.category.".$prefix.".key"));
                }
            } elseif ($page->is_form) {
                // カスタムフォーム項目取得
                $search['form_type'] = config("pacd.form.type.$page->route_name.key");
                if (!isCurrent('admin.*')) {
                    $search['is_display_published'] = 1;
                }

                $data['inputs'] = FormInput::where($search)->whereIn('event_id',[0,$event_id])->get()->keyBy('id');
            }
        }
        return $data ?? [];
    }
}
