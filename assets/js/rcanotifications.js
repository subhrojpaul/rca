var ll_not_start=0, ll_not_init_size=10, ll_not_size=10;
function getNotList(){
	ajax({method:'ajax_get_notifications_list',start:ll_not_start,limit:ll_not_start+(ll_not_start==0?ll_not_init_size:ll_not_size)+1},getNotListSuccess);
	ll_not_start+=(ll_not_start==0?ll_not_init_size:ll_not_size);
}
function getNotListSuccess(res){
	console.log(res);
	
	var notlist=res.data.not_list;
	$t=$('._noty_box');
	var more=false;
	if (notlist.length>(ll_not_start==ll_not_init_size?ll_not_init_size:ll_not_size)) {
		more=true;
		notlist.splice((ll_not_start==ll_not_init_size?ll_not_init_size:ll_not_size));
	}
	notlist.forEach(function(not){
		var html='';
		html+='<div class="_noty_row" style="cursor:pointer" data-entity="'+not.link_to_entity+'" data-entity-pk="'+not.link_to_entity_pk+'" onclick="openEntity($(this))">';
        html+='	<div class="_noty_img"><img src="'+(not.notification_icon_url==''?'images/noty.png':not.notification_icon_url)+'" width="30" /></div>';
        html+='	<div class="_noty_text">';
        html+='		<p>'+not.body+'</p>';
        html+='		<span>'+not.time_ago+'</span>';
        html+='	</div>';
        html+='</div>';
        $t.append(html);
	});
	if (more) $t.append('<button id="getMoreNotsBtn" class="__btn_sm" onclick="getMoreNots()" style="margin-top:5px;">MORE...</button>');
}
function openEntity($t) {
	var fields={'LOT':'lot_id','APPL':'app_id','APPL_SERVICE':'app_service_id'};
	var $f=$('<form action="groupapps.php" method="get"><input name="'+fields[$t.data('entity')]+'" value="'+$t.data('entity-pk')+'"></form>');
	$('body').append($f);
	$f[0].submit();
}
function getMoreNots(){
	$('#getMoreNotsBtn').remove();
	getNotList();
}
function scrollLoad(){
	if($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight) {
		if ($('#getMoreNotsBtn').length>0) getMoreNots();//call function to load content when scroll reachs DIV bottom          
	}
}
function notBoxResize() {
	if ($(window).width()>991) {
		$('._noty_box').height($(window).height()-$('._noty_box').offset().top-25).css('overflow','auto');
	} else {
		$('._noty_box').height('auto');
	}
}
$(document).ready(function(){
	$(window).resize(notBoxResize);
	$('._noty_box').on('scroll',scrollLoad);
	notBoxResize();
	getNotList();
});
