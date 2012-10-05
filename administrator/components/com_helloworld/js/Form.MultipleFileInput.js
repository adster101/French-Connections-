/*
---

name: Form.MultipleFileInput
description: Create a list of files that has to be uploaded
license: MIT-style license.
authors: Arian Stolwijk
requires: [Element.Event, Class, Options, Events]
provides: Form.MultipleFileInput

...
*/


Object.append(Element.NativeEvents, {
	dragenter: 2, dragleave: 2, dragover: 2, dragend: 2, drop: 2
});

if (!this.Form) this.Form = {};

Form.MultipleFileInput = new Class({

	Implements: [Options, Events],

	options: {
    dropMsg: 'Drag and drop images here.',
		itemClass: 'uploadItem'/*,
		onAdd: function(file){},
		onRemove: function(file){},
		onEmpty: function(){},
		onDragenter: function(event){},
		onDragleave: function(event){},
		onDragover: function(event){},
		onDrop: function(event){}*/
	},

	_files: [],

	initialize: function(input, list, drop, options){
		input = this.element = document.id(input);
		list = this.list = document.id(list);
		drop = this.drop = document.id(drop);

		this.setOptions(options);
		var name = input.get('name');
		if (name.slice(-2) != '[]') input.set('name', name + '[]');
		input.set('multiple', true);

		this.inputEvents = {
			change: function(){
				Array.each(input.files, this.add, this);
			}.bind(this)
		};

		this.dragEvents = drop && (typeof document.body.draggable != 'undefined') ? {
			dragenter: this.fireEvent.bind(this, 'dragenter'),
			dragleave: this.fireEvent.bind(this, 'dragleave'),
			dragend: this.fireEvent.bind(this, 'dragend'),
			dragover: function(event){
				event.preventDefault();
				this.fireEvent('dragover', event);
			}.bind(this),
			drop: function(event){
				event.preventDefault();
				var dataTransfer = event.event.dataTransfer;
				if (dataTransfer) Array.each(dataTransfer.files, this.add, this);
				this.fireEvent('drop', event);
			}.bind(this)
		} : null;

		this.attach();
	},

	attach: function(){
		this.element.addEvents(this.inputEvents);
		if (this.dragEvents) this.drop.addEvents(this.dragEvents);
	},

	detach: function(){
		this.input.removeEvents(this.inputEvents);
		if (this.dragEvents) this.drop.removeEvents(this.dragEvents);
	},

	add: function(file){
		this._files.push(file);
		var self = this;
		new Element('li', {
			'class': this.options.itemClass
		}).adopt(new Element('span', {
			text: ' '+file.name
		}).grab(new Element('i',{
      'class':'boot-icon-file'
    }),'top')).grab(new Element('span',{
      text: '(' + file.size / 1000 + ' KBytes)'
    })).grab(new Element('span',{'class':'image-queue-delete'}).grab(new Element('a', {
			href: '#',
			events: {click: function(e){
				e.preventDefault();
				self.remove(file);
			}}
		}))).inject(this.list);
		this.fireEvent('add', file);
    if (this._files.length > 0) {
      $$('.upload-queue-drag').destroy();
    } 
		return this;
	},

	remove: function(file) {
		var index = this._files.indexOf(file);
		if (index == -1) return this;
		this._files.splice(index, 1);
		this.list.childNodes[index].destroy();
		this.fireEvent('remove', file);
		if (!this._files.length) this.fireEvent('empty');
    
    if (this._files.length == 0) {
      
      drop = $('droppable');
      console.log(drop);
 
      var elem = new Element('li.upload-queue-drag', {
        text: this.options.dropMsg
      }).inject(drop, 'top');
    }
    
		return this;
	},
  unsetFiles: function() {
    return this._files = [];
  },
	getFiles: function(){
		return this._files;
	}

});
