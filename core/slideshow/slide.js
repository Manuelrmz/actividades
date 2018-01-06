(function($)
{
	var options = {index : 0,size : 0, element:undefined,indexloaded:0};
	var methods = {
		init : function()
		{
			if($(this).children(".slidecontent").length < 1)
			{
				options.element = this;
				$(this).append("<div class='slidecontent'><div class='slide'></div><div class='text'></div><div class='number'></div><a class='prev'>❮</a><a class='next'>❯</a></div>");
			}
			$(document).on('click','.prev',function(){methods.change(-1)});
			$(document).on('click','.next',function(){methods.change(1)});
		},
		addImagesContent : function(images)
		{
			if(Object.prototype.toString.call(images) === '[object Array]')
			{
				options.index = 1;
				options.indexloaded = 0;
				var contenedor = options.element.children('.slidecontent').children(".slide");
				// var currentSize = this.width();
				// var currenHeight = this.height();
				contenedor.html("");
				for(var i = 0; i < images.length; i ++)
	            {
	            	$("<img class='fade'/>").attr("src", images[i]).load(function()
	            	{
				     //    var pic_width = this.width;
				     //    var pic_heigth = this.height;
				     //    var ratiowidheig = pic_heigth/pic_width;
				     //    if(pic_width > currentSize)
				     //    {
				     //    	pic_width = currentSize;
				     //    	pic_heigth = Math.round(currentSize*ratiowidheig);
				     //    }
				     //    $(this).attr('width',pic_width+"px");
					    // $(this).attr('height',pic_heigth+"px");
				    	contenedor.append(this);
				    	options.indexloaded++;
				    	if(images.length == options.indexloaded)
				    		methods._showSlides(options.index);
				    });
	            }
	        }
		},
		resetContent: function()
		{
			var contenedor = options.element.children('.slidecontent').children(".slide");
			contenedor.html("");
		},
		change : function(n)
		{
			methods._showSlides(options.index += n);
		},
		current: function(index){
			console.log(options);
		},
		_showSlides : function(n)
		{
			var currentImage =document.getElementsByClassName("slide")[0].getElementsByTagName('img');
			var numberDiv = options.element.children('.slidecontent').children('.number');
			if(n > currentImage.length)
				options.index = 1;
			if(n < 1)
				options.index = currentImage.length;
			for(i = 0; i < currentImage.length; i ++)
			{
				currentImage[i].style.display = "none";
			}
			currentImage[options.index-1].style.display = "block";
			numberDiv.html(options.index+"/"+currentImage.length);
		}
	};
	$.fn.slideShow = function(method)
	{
		if(methods[method])
			return methods[method].apply(this,Array.prototype.slice.call( arguments, 1 ));
		else
			$.error("Method: "+method+" Doesn't exist on jQuery.slideShow plugin");
	};
})(jQuery);
