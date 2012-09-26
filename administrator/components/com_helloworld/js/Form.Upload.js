/*
---

name: Form.Upload
description: Create a multiple file upload form
license: MIT-style license.
authors: Arian Stolwijk
requires: [Form.MultipleFileInput, Request.File]
provides: Form.Upload

...
*/

if (!this.Form) this.Form = {};

Form.Upload = new Class({

	Implements: [Options, Events],

	options: {
		dropMsg: 'Drag and drop images here.',
		onComplete: function(){
			// reload
			window.location.href = window.location.href;
		}
	},

	initialize: function(input, options){
		input = this.input = document.id(input);

		this.setOptions(options);

		// Our modern file upload requires FormData to upload
		if ('FormData' in window) this.modernUpload(input);
		else this.legacyUpload(input);
	},

	modernUpload: function(input){

		this.modern = true;

		var form = input.getParent('form');
		if (!form) return;

		var self = this,

			drop = new Element('ul.droppable#droppable', {
			}).inject(input, 'after'),

			list = new Element('li.upload-queue-drag', {
        text: this.options.dropMsg
      }).inject(drop, 'top'),

			progress = new Element('div.progress')
				.setStyle('display', 'none').inject(input, 'after'),

			inputFiles = new Form.MultipleFileInput(input, drop, drop, {
				onDragenter: drop.addClass.pass('hover', drop),
				onDragleave: drop.removeClass.pass('hover', drop),
				onDrop: drop.removeClass.pass('hover', drop)
			}),

			uploadReq = new Request.File({
				url: form.get('action'),
				onRequest: progress.setStyles.pass({display: 'block', width: 0}, progress),
				onProgress: function(event){
					var loaded = event.loaded, total = event.total;
					progress.setStyle('width', parseInt(loaded / total * 100, 10).limit(0, 100) + '%');
				},
				onComplete: function(){
					progress.setStyle('width', '100%');
					self.fireEvent('complete', arguments);
        
          this.formData = new FormData();		
          // Remove the file elements from the HTML node
          list.empty();
          // Hide the progress-o-meter
          progress.setStyle('display', 'none');
          // Remove the files from the inputFiles array otherwise they persist
          inputFiles.unsetFiles();
        }
			}),

			inputname = input.get('name');

		form.addEvent('submit', function(event){
			event.preventDefault();
			inputFiles.getFiles().each(function(file){
				uploadReq.append(inputname , file);
			});
			uploadReq.send();
		});

	},

	legacyUpload: function(input){
    // Make this work!
		//var row = input.getParent('.formRow');
			//rowClone = row.clone(),
			//add = function(event){
				//event.preventDefault();
				//var newRow = rowClone.clone();

				//newRow.getElement('input').grab(new Element('a.delInputRow', {
					//text: 'x',
					//events: {click: function(event){
						//event.preventDefault();
						//newRow.destroy();
					//}}
				//}), 'after');

				//newRow.inject(row, 'after');
			//};

		//new Element('a.addInputRow', {
			//text: '+',
			//events: {click: add}
		//}).inject(input, 'after');

	},

	isModern: function(){
		return !!this.modern;
	}

});
