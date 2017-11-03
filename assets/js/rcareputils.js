//report utils
var _ll_start=0, _ll_limit=20, _ll_more=false, _ll_func=null, _ll_more_func=null;
$(function() {
    _rca_initReport();
});
function _rca_initReport() {
    $r=$('.report_table');
    $rh=$('.report_table').clone();
    $rh.addClass('header').css({ height: "40px", overflow: "hidden", "z-index": 1, position: "absolute", width: "100%"});
    $r.addClass('data').css({overflow:"auto"}).before($rh);
    _rca_winResize();
    $(window).resize(_rca_winResize);
    _ll_func = $('.report_table').data('report-function');
    _ll_more_func=$('.report_table').data('report-lazyload-function');
    $r.scroll(_rca_reportLazyLoad);
    window[_ll_func]();
}
function _rca_applySearch(){
    $('.report_table>table>tbody').html('');
    _ll_start=0;
    window[_ll_func]();
}
function _rca_reportLazyLoad(){
    if(_ll_more && $(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight) {
        if (typeof _ll_more_func==='function') window[_ll_more_func]();
        else window[_ll_func]();   
    }
}
function _rca_getReportData(ajaxfunction,search,filters,multisort){
    console.log({method:ajaxfunction,start:_ll_start,limit:_ll_limit+1,search_str:search,filters:filters,multi_sort:multisort});
    ajax({method:ajaxfunction,start:_ll_start,limit:_ll_limit+1,search_str:search,filters:filters,multi_sort:multisort},_rca_getReportDataSuccess);
    _ll_start+=_ll_limit;
}
function _rca_getReportDataSuccess(res){
    //console.log(res);
    var repdata=res.data.repdata;
    var header=res.data.repdata.header||[];
    var detail=res.data.repdata.detail||[];
    //if (detail.length==0) return;
    $h=$('.report_table>table>thead>tr');
    $t=$('.report_table>table>tbody');
    if (detail.length>_ll_limit) {
        _ll_more=true;
        detail.splice(_ll_limit);
    } else _ll_more=false;
    if (detail.length==0 & $('.report_table table tbody tr[data-row="app-row"]').length==0) {
        $t.append('<tr><td colspan="7" style="text-align:center">No records found. Please review</td></tr>');
    }
    $h.html('');
    var headerHTML='';
    header.forEach(function(h){
        headerHTML+='<th>'+h.display_name+'</th>';
    });
    $h.html(headerHTML);
    detail.forEach(function(d){
        var html='';
        html+='<tr data-row="app-row">';
        var c=0;
        header.forEach(function(h){
            html+='<td>'+nvl(d[c++])+'</td>';
        });
        html+='</tr>';
        $t.append(html);
    });
}
function _rca_winResize(){
    $('.report_table.data').height($(window).height()-$('.report_table.data').offset().top-10);
}
function _rca_getDownloadExcel(excelfunction,search,filters,multisort){
    var params={method:excelfunction,search:search,filters:filters,multisort:multisort};
    var qString="params="+encodeURIComponent(JSON.stringify(params));
    window.open('../handlers/rcaexceldownload.php?'+qString,'_blank');
}