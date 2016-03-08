$(document).ready(function() {
	console.log('ready');
	
	var target = $('body')[0];
	
	// создаем новый экземпляр наблюдателя
	var observer = new MutationObserver(function(mutations) {
		mutations.forEach(function(mutation) {
			if(mutation.type == 'childList' && mutation.addedNodes[0]) {
				var jqItem = $(mutation.addedNodes[0]);
				var iItemID = $(mutation.addedNodes[0]).attr('id');
				
				if(iItemID == 'popupFM') {
					ImgPositioner.setButton();
				}
			}
		});    
	});

	// создаем конфигурации для наблюдателя
	var config = { attributes: true, childList: true, characterData: true };

	// запускаем механизм наблюдения
	observer.observe(target,  config);
});


var ImgPositioner = {
	active: false,
	
	setButton: function() {
		$('.adm-photoeditor-buttons-panel .adm-photoeditor-btn-wrap')
			.append('<span class="adm-photoeditor-btn adm-photoeditor-btn-img-positioner" id="ImgPositioner" title="Определить центр картинки"><span class="adm-photoeditor-btn-icon"></span></span>');
	
		$('#FMeditorActiveImageBlockOuter')
			.append('<div class="img-positioner-info" style="display: none;"></div>');
	
		$(document).on('click', '#ImgPositioner', function() {
			$('#ImgPositioner').toggleClass('active');
			$('.img-positioner-info').toggle();
				
			if(this.active) {
				this.active = false;
				
			} else {
				this.active = true;
				ImgPositionerSetCoord(this.active);
				
			}
		});
	}
};

function ImgPositionerSetCoord(active) {
	if(active) {
		var h = $('#FMeditorActiveImageBlock').height();
		var w = $('#FMeditorActiveImageBlock').width();
		
		$(document).on('mousemove', '#FMeditorActiveImageCanvas', function(e) {
			ImgPositionerCalcPos(e, this);
		});
		
		$(document).on('click', '#FMeditorActiveImageCanvas', function(e) {
			var pos = ImgPositionerCalcPos(e, this);
			ImgPositionerSave(pos);
		});
	}
}

function ImgPositionerCalcPos(event, object) {
	var h = $('#FMeditorActiveImageBlock').height();
	var w = $('#FMeditorActiveImageBlock').width();
	
	var x = event.offsetX - object.offsetLeft;
	var y = event.offsetY - object.offsetTop;

	var x_percent = Math.round(x * 100 / w);
	var y_percent = Math.round(y * 100 / h);

	$('.img-positioner-info').html(x_percent+'<br>'+y_percent);
	
	return [x_percent, y_percent];
}

function ImgPositionerSave(pos) {
	var pattern		= /FM_([0-9]+)EditorItem/i;
	var sQueueId	= $('#FMeditorQueueInner .active').attr('id');
	var arMatches	= sQueueId.match(pattern);
	var iFileID		= parseInt(arMatches[1]);
	
	
	if(Number.isInteger(iFileID)) { console.log('var iFileID = '+iFileID);
		$.ajax({
			type: "GET",
			url: "/local/modules/img.positioner/ajax/save.php",
			data: ({
				ID	: iFileID,
				X	: pos[0],
				Y	: pos[1]
			}),
			success: function(data){
				console.log('save result: ', iFileID, pos, data);
			}
		});
	}
}
