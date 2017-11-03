var PAUtils=function(){}
PAUtils.message=function(o) {
	var $=jQuery;
	PAUtils.message.id = PAUtils.message.id||"";
	if (PAUtils.message.id=="") PAUtils.message.id='-pa-fw-message-'+Math.random().toString().replace('.','');
	var id=PAUtils.message.id, $m =$('#'+id);
	if ($m.length==0) {
		$m=$('<div class="modal" id="'+id+'"><div class="modal-dialog" role="document"><div class="modal-content"><div class="modal-header"><strong class="modal-title"></strong><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div><div class="modal-body"></div></div></div></div>');
		$('body').append($m);
	} 
	$m.find('.modal-title').html(o.title);
	$m.find('.modal-body').html(o.message);
	$m.find('.modal-footer').remove();
	$f=$('<div class="modal-footer"></div>');
	$m.find('.modal-content').append($f);
	if (typeof o.buttons!=='undefined' && o.buttons.length>0) {
		$.each(o.buttons, function(k,v) {
			$b=$('<button type="button" class="btn'+(v.primary?' btn-primary':'')+'" style="margin-left:20px">'+v.label+'</button>');
			if (typeof v.callback === "function" ) {
				$b.click(function(event){
					$m.modal('hide');
					v.callback(event);
				});
			} else $b.click(function(){ $m.modal('hide'); });
			$f.append($b);
		});
	} else $f.append('<button type="button" class="btn" data-dismiss="modal" aria-label="Close">Ok</button>');

	if ($f.find('button.btn-primary').length==0) $f.find('button').first().addClass('btn-primary');
	$m.modal('show');
	$f.find('button.btn-primary').first().focus();
}

PAUtils.debounce=function(f, w, i) {
	var t;
	return function() {
		var c = this, a = arguments, l = function() { t = null; if (!i) f.apply(c, a); };
		var cN = i && !t;
		clearTimeout(t);
		t = setTimeout(l, w);
		if (cN) f.apply(c, a);
	};
};