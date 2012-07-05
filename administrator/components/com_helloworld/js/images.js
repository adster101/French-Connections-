window.addEvent('domready', function() {

	var progressbarOptions = {

			width: 140, //fix the progressbar width
			color: '#000', 
			fillColor: '#fff',
			text: 'Pending...',
			onChange: function (value, progressbar) {
			
				//console.log(arguments)
				progressbar.setText('completed: ' + (100 * value).format() + '%')
			}
		},
		options = {
			/*
				upload restrictions
			*/
			
			base: '../php/upload.php',
			
			//you cannot upload more than 3 files
			limit: 3,
			
			//show the pause/resume upload by default if the browser can handle that otherwise show only on error
			pause: true,
			
			//each file size must be less than 2.5Mb
			filesize: 2621440,
			
			//total files size must not exceed 3.5Mb
			maxsize: 3670016,
			
			//use iframe
			iframe: false,
			
			//upload container id
			container: 'upload',
			
			//filter uploaded file type
			//filetype: 'html,rar,zip',
			
			//upload field name
			name: 'alias',
			//limit: 2, /* limit to two downloads */
			multiple: true, //enable multiple selection in file dialog
			progressbar: progressbarOptions,
			onCreate: function () {
			
				//no progressbar, we display a plain text instead
				if(!this.options.progressbar) {
				
					var bar = new Element('span[style=width:140px;text-align:center;display:inline-block][text=\\[upload\\] Pending ...]').inject(this.element, 'top');
					
					this.addEvent('progress', function (value) {
					
						bar.innerHTML = '[upload] ' + (value * 100).format() + '%';
						
						if(value == 1) bar.destroy.delay(50, bar)
					})
				}
			},
			onAbort: function (object) {
			
				alert('Upload aborted:\n' + object.message)
			},
			onCancel: function (obj) {
			
				//upload size
				$('infos').set('html', uploadManager.getSize('upload').toFileSize());
				if(obj.message) alert('Upload cancelled:\n' + obj.message)
				
			},
			onSuccess: function (infos) {
							
				//upload size
				$('infos').set('html', uploadManager.getSize('upload').toFileSize());
				//console.log(infos.file);
				//download link
				new Element('span', {html: '<a href="../php/upload.php?' + infos.path + '&dl=1&filename=' + infos.file + '">download</a> -&nbsp;'}).inject($(infos.transfer).getElement('a'), 'before')
			},
			onAllComplete: function (container) {
			
				var message = 'all transfers completed for "' + container + '"';
				
				if(window.console && console.log) console.log(message);
				else alert(message);
			}
		},
		fn = function () { uploadManager.enqueue = this.checked },
		fr = function () { options.iframe = this.checked },
		mu,
		pr,
		dd = document.getElement('label[for=dd]');
	
	if(uploadManager.multiple) {
	
		mu = function () { options.multiple = this.checked };
		$('multiple').addEvents({click: mu, change: mu, checked: true}).disabled = false;
	}
	
	if(uploadManager.xmlhttpupload) {
	
		mu = function () { options.iframe = this.checked };
		$('iframe').addEvents({click: mu, change: mu}).disabled = false;
	}
	
	if(uploadManager.xmlhttpupload) {
	
		pr = function () { options.progressbar = this.checked ? progressbarOptions : false };
		$('progressbar').addEvents({click: pr, change: pr}).set({disabled: false, checked: true});
	}
	
	//Modernizr.draganddrop && window.FileReader - see https://github.com/Modernizr/Modernizr/issues/57
	dd.set('text', dd.get('text') + (window.DataTransfer ? ' (Yes!)' : ' ... don\'t know :('));
	dd.getPrevious().disabled = true;
	
	//enable/disable queue
	uploadManager.enqueue = $('queue').addEvents({click: fn, change: fn}).checked;
	uploadManager.iframe = $('iframe').set({events: {click: fr, change: fr}, disabled: !uploadManager.xmlhttpupload, checked: !uploadManager.xmlhttpupload }).checked;
	$('pa').checked = uploadManager.resume;
	
	uploadManager.attachDragEvents('upload', options);
	
	document.getElement('a').addEvent('click', function(e) {
	
		e.stop();
		uploadManager.upload(options) 
	});
	
	// submit form handler
	document.id(document.forms[0]).addEvents({
		
		// remove files on reset
		reset: function () {
		
			uploadManager.getTransfers('upload').invoke('cancel')
		},
		submit: function (e) {
	
			e.stop();
			
			// check file uploads
			var transfers = uploadManager.getTransfers('upload');
			
			if(transfers.length == 0) {
			
				alert('You must select a file!');
				return;
			}
			
			// check transfer state. are some transfer incomplete ?
			if(transfers.some(function (transfer) { return transfer.state != 4 })) {
			
				alert('Some transfers are not complete');
				return
			}
			
			// check the number if files uploaded
			// if(transfers.length < 3) {
			
			// 	alert('Please upload more files');
			// 	return
			// }
			
			alert('Upload complete')
		}
	})
})
