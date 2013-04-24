<?
require_once('phpQuery.php');
require_once('workflows.php');

function search($kw) {
    $wf = new Workflows();
    $url = 'http://www.bilibili.tv/search?keyword='.urlencode($kw).'&orderby=&formsubmit=';
    $content = $wf->request($url, array(CURLOPT_ENCODING => 1));
    $doc = phpQuery::newDocumentHTML($content);
    $list = $doc->find('li.l');

    $i = 0;
    foreach ($list as $item) {
        $link = pq($item)->children('a:first')->attr('href');
        if (strpos($link, 'http') !== 0) {
            $link = 'http://www.bilibili.tv'.$link;
        }
        $info = pq($item)->find('div.info > i');
        $author = pq($item)->find('a.upper:first')->text();
        $view = $info->eq(0)->text();
        $comment = $info->eq(1)->text();
        $bullet = $info->eq(2)->text();
        $save = $info->eq(3)->text();
        $date = $info->eq(4)->text();
        $subtitle = 'UP主:'.$author.' 播放:'.$view.' 评论:'.$comment.' 弹幕:'.$bullet.' 收藏:'.$save.' 日期:'.$date;
        $wf->result(
            $i,
            $link,
            pq($item)->find('div.t:first')->text(),
            $subtitle,
            '',
            'yes'        
        );
        $i++;
    }

    if (count($wf->results()) == 0) {
        $wf->result('0', $url, '在bilibili.tv中搜索', $kw, '', 'yes');
    }

    return $wf->toxml();
}
?>