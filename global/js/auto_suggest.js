/**
 * Plugin Name: Autocomplete for Textarea
 * Author: Amir Harel
 * Copyright: amir harel (harel.amir1@gmail.com)
 * Twitter: @amir_harel
 * Version 1.4
 * Published at : http://www.amirharel.com/2011/03/07/implementing-autocomplete-jquery-plugin-for-textarea/
 */

(function($){
	/**
	 * @param obj
	 * 	@attr wordCount {Number} the number of words the user want to for matching it with the dictionary
	 *	@attr mode {String} set "outter" for using an autocomplete that is being displayed in the outter layout of the textarea, as opposed to inner display
	 * 	@attr on {Object} containing the followings:
	 * 		@attr query {Function} will be called to query if there is any match for the user input
	 */
	$.fn.autocomplete = function(obj){
		if( typeof $.browser.msie != 'undefined' ) obj.mode = 'outter';
		this.each(function(index,element){
			if( element.nodeName == 'TEXTAREA' ){
				makeAutoComplete(element,obj);
			}
		});		
	}
	
	var browser =  {isChrome: $.browser.webkit };
	
	function getTextAreaSelectionEnd(ta) {
		 var textArea = ta;//document.getElementById('textarea1');
		 if (document.selection) { //IE
			var bm = document.selection.createRange().getBookmark();
			var sel = textArea.createTextRange();
			sel.moveToBookmark(bm);
			var sleft = textArea.createTextRange();
			sleft.collapse(true);
			sleft.setEndPoint("EndToStart", sel);		  
			return sleft.text.length + sel.text.length;		  
		}
		return textArea.selectionEnd;	//ff & chrome 
	}
	
	function getDefaultCharArray(){
		return  {
		'`':0,
		'~':0,
		'1':0,
		'!':0,
		'2':0,
		'@':0,
		'3':0,
		'#':0,
		'4':0,
		'$':0,
		'5':0,
		'%':0,
		'6':0,
		'^':0,
		'7':0,
		'&':0,
		'8':0,
		'*':0,
		'9':0,
		'(':0,
		'0':0,
		')':0,
		'-':0,
		'_':0,
		'=':0,
		'+':0,
		'q':0,
		'Q':0,
		'w':0,
		'W':0,
		'e':0,
		'E':0,
		'r':0,
		'R':0,
		't':0,
		'T':0,
		'y':0,
		'Y':0,
		'u':0,
		'U':0,
		'i':0,
		'I':0,
		'o':0,
		'O':0,
		'p':0,
		'P':0,
		'[':0,
		'{':0,
		']':0,
		'}':0,
		'a':0,
		'A':0,
		's':0,
		'S':0,
		'd':0,
		'D':0,
		'f':0,
		'F':0,
		'g':0,
		'G':0,
		'h':0,
		'H':0,
		'j':0,
		'J':0,
		'k':0,
		'K':0,
		'l':0,
		'L':0,
		';':0,
		':':0,
		'\'':0,
		'"':0,
		'\\':0,
		'|':0,
		'z':0,
		'Z':0,
		'x':0,
		'X':0,
		'c':0,
		'C':0,
		'v':0,
		'V':0,
		'b':0,
		'B':0,
		'n':0,
		'N':0,
		'm':0,
		'M':0,
		',':0,
		'<':0,
		'.':0,
		'>':0,
		'/':0,
		'?':0,
		' ':0
		};
	}
	
		
	function setCharSize(data){
		for( var ch in data.chars ){
			if( ch == ' ' ) $(data.clone).html("<span id='test-width_"+data.id+"' style='line-block'>&nbsp;</span>");
			else $(data.clone).html("<span id='test-width_"+data.id+"' style='line-block'>"+ch+"</span>");
			var testWidth = $("#test-width_"+data.id).width();
			data.chars[ch] = testWidth;
		}		
	}
	
	var _data = {};
	var _count = 0;
	function makeAutoComplete(ta,obj){
		_count++;
		_data[_count] = {
			id:"auto_"+_count,
			ta:ta,
			wordCount:obj.wordCount,
			wrap: obj.wrap,
			on:obj.on,
			clone:null,
			lineHeight:0,
			list:null,
			charInLines:{},
			mode:obj.mode,
			chars:getDefaultCharArray()};
			
		var clone = createClone(_count);
		_data[_count].clone = clone;
		setCharSize(_data[_count]);
		//_data[_count].lineHeight = $(ta).css("font-size");
		_data[_count].list = createList(_data[_count]);	
		registerEvents(_data[_count]);	
	}
	
	function createList(data){
		var ul = document.createElement("ul");
		$(ul).addClass("auto-list");
		document.body.appendChild(ul);
		return ul;
	}
	
	function createClone(id){
		var data = _data[id];
		var div = document.createElement("div");
		var offset = $(data.ta).offset();
		offset.top = offset.top - parseInt($(data.ta).css("margin-top"));
		offset.left = offset.left - parseInt($(data.ta).css("margin-left"));
		//console.log("createClone: offset.top=",offset.top," offset.left=",offset.left);
		$(div).css({
			position:"absolute",
			top: offset.top,
			left: offset.left,
			"overflow-x" : "hidden",
			"overflow-y" : "hidden",
			"z-index" : -10
		});

		data.chromeWidthFix = (data.ta.clientWidth - $(data.ta).width());
		data.lineHeight = $(data.ta).css("line-height");
		if( isNaN(parseInt(data.lineHeight)) ) data.lineHeight = parseInt($(data.ta).css("font-size"))+2;
				
		document.body.appendChild(div);
		return div;		
	}

	function getWords(data){
		var selectionEnd = getTextAreaSelectionEnd(data.ta);//.selectionEnd;
		var text = data.ta.value;
		text = text.substr(0,selectionEnd);
		if( text.charAt(text.length-1) == ' ' || text.charAt(text.length-1) == '\n' ) return "";
		var ret = [];
		var wordsFound = 0;
		var pos = text.length-1;
		while( wordsFound < data.wordCount && pos >= 0 && text.charAt(pos) != '\n'){
			ret.unshift(text.charAt(pos));
			pos--;
			if( text.charAt(pos) == ' ' || pos < 0 ){
				wordsFound++;
			}
		}
		return ret.join("");		
	}
	
	
	function showList(data,list,text){
		if( !data.listVisible ){
			data.listVisible = true;
			var pos = getCursorPosition(data);
			$(data.list).css({
				left: pos.left+"px",
				top: pos.top+"px",
				display: "block"
			});
			
		}		
		
		var html = "";
		var regEx = new RegExp("("+text+")");
		var taWidth = $(data.ta).width()-5;
		var width = data.mode == "outter" ? "style='width:"+taWidth+"px;'" : "";
		for( var i=0; i< list.length; i++ ){

			//var a = list[i].replace(regEx,"<mark>$1</mark>");
			 
			
			html += "<li data-value='"+list[i]+"' "+width+">"+list[i].replace(regEx,"<mark>$1</mark>")+"</li>";
		}
		$(data.list).html(html);
	}	
	
	function breakLines(text,data){
		var lines = [];
		
		var width = $(data.clone).width();
		
		var line1 = "";
		var line1Width = 0;
		var line2Width = 0;
		var line2 = "";
		var chSize = data.chars;
		
		
		var len = text.length;
		for( var i=0; i<len; i++){
			var ch = text.charAt(i);
			line2 += ch.replace(" ","&nbsp;");
			var size = (typeof chSize[ch] == 'undefined' ) ? 0 : chSize[ch];
			line2Width += size;
			if( ch == ' '|| ch == '-' ){
				if( line1Width + line2Width < width-1 ){
					line1 = line1 + line2;
					line1Width = line1Width + line2Width;
					line2 = "";
					line2Width = 0;
				}
				else{
					lines.push(line1);
					line1= line2;
					line1Width = line2Width;
					line2= "";
					line2Width = 0;
				}
			}
			if( ch == '\n'){
				if( line1Width + line2Width < width-1 ){
					lines.push(line1 + line2);					
				}
				else{
					lines.push(line1);
					lines.push(line2);					
				}
				line1 = "";
				line2 = "";
				line1Width = 0;
				line2Width = 0;
			}
			//else{
				//line2 += ch;	
			//}			
		}
		if( line1Width + line2Width < width-1 ){
			lines.push(line1 + line2);			
		}
		else{
			lines.push(line1);
			lines.push(line2);
		}
		return lines;
	}
	
	
	
	
	function getCursorPosition(data){
		if( data.mode == "outter" ){
			return getOuterPosition(data);
		}
		//console.log("getCursorPosition: ta width=",$(data.ta).css("width")," ta clientWidth=",data.ta.clientWidth, "scrollWidth=",data.ta.scrollWidth," offsetWidth=",data.ta.offsetWidth," jquery.width=",$(data.ta).width());
		if( browser.isChrome ){
			$(data.clone).width(data.ta.clientWidth-data.chromeWidthFix);
		}
		else{
			$(data.clone).width(data.ta.clientWidth);
		}
		
		
		var ta = data.ta;
		var selectionEnd = getTextAreaSelectionEnd(data.ta);
		var text = ta.value;//.replace(/ /g,"&nbsp;");
		
		var subText = text.substr(0,selectionEnd);
		var restText = text.substr(selectionEnd,text.length);
		
		var lines = breakLines(subText,data);//subText.split("\n");
		var miror = $(data.clone);
		
		miror.html("");
		for( var i=0; i< lines.length-1; i++){
			miror.append("<div style='height:"+(parseInt(data.lineHeight))+"px"+";'>"+lines[i]+"</div>");
		}
		miror.append("<span id='"+data.id+"' style='display:inline-block;'>"+lines[lines.length-1]+"</span>");
		
		miror.append("<span id='rest' style='max-width:'"+data.ta.clientWidth+"px'>"+restText.replace(/\n/g,"<br/>")+"&nbsp;</span>");
		
		miror.get(0).scrollTop = ta.scrollTop;
		
		var span = miror.children("#"+data.id);
		var offset = span.offset();
		
		return {top:offset.top+span.height(),left:offset.left+span.width()};
		
	}
	
	function getOuterPosition(data){
		var offset = $(data.ta).offset();
		return {top:offset.top+$(data.ta).height()+8,left:offset.left};
	}
	
	function hideList(data){
		if( data.listVisible ){
			$(data.list).css("display","none");
			data.listVisible = false;
		}
	}
	
	function setSelected(dir,data){
		var selected = $(data.list).find("[data-selected=true]");
		if( selected.length != 1 ){
			if( dir > 0 ) $(data.list).find("li:first-child").attr("data-selected","true");
			else $(data.list).find("li:last-child").attr("data-selected","true");
			return;
		}
		selected.attr("data-selected","false");
		if( dir > 0 ){
			selected.next().attr("data-selected","true");	
		}
		else{
			selected.prev().attr("data-selected","true");
		}
	}
	
	function getCurrentSelected(data){
		var selected = $(data.list).find("[data-selected=true]");
		if( selected.length == 1) return selected.get(0);
		return null;
	}
	
	function onUserSelected(li,data){
		var seletedText = $(li).attr("data-value");
				
		var selectionEnd = getTextAreaSelectionEnd(data.ta);//.selectionEnd;
		var text = data.ta.value;
		text = text.substr(0,selectionEnd);
		
		//if( text.charAt(text.length-1) == ' ' || text.charAt(text.length-1) == '\n' ) return "";
		//var ret = [];
		var wordsFound = 0;
		var pos = text.length-1;
		
		while( wordsFound < data.wordCount && pos >= 0 && text.charAt(pos) != '\n'){
			//ret.unshift(text.charAt(pos));
			pos--;
			if( text.charAt(pos) == ' ' || pos < 0 ){
				wordsFound++;
			}
		}
		var a = data.ta.value.substr(0,pos+1);
		var c = data.ta.value.substr(selectionEnd,data.ta.value.length);
		var scrollTop = data.ta.scrollTop;
		
		if(data.wrap.length > 0){
            seletedText = "["+data.wrap+"]"+seletedText+"[/"+data.wrap+"] ";
        }
		
		data.ta.value = a+seletedText+c;
		data.ta.scrollTop = scrollTop;
		data.ta.selectionEnd = pos+1+seletedText.length;
		hideList(data);
		$(data.ta).focus();	
	}
	
	function registerEvents(data){
		$(data.list).delegate("li","click",function(e){
			var li = this;
			onUserSelected(li,data);
			e.stopPropagation();
			e.preventDefault();
			return false;
		});
		
		
		
		$(data.ta).blur(function(e){
			setTimeout(function(){
				hideList(data);	
			},400);
			
		});
		
		$(data.ta).click(function(e){
			hideList(data);
		});
		
		$(data.ta).keydown(function(e){
			//console.log("keydown keycode="+e.keyCode);
			if( data.listVisible ){
				switch(e.keyCode){
					case 13:
					case 40:
					case 38:
						e.stopImmediatePropagation();
						e.preventDefault();
						return false;
					case 27: //esc
						hideList(data);
				}
				
			}
		});
		
		$(data.ta).keyup(function(e){
			if( data.listVisible ){
				//console.log("keCode=",e.keyCode);
				if( e.keyCode == 40 ){//down key
					setSelected(+1,data);
					e.stopImmediatePropagation();
					e.preventDefault();
					return false;
				}
				if( e.keyCode == 38 ){//up key
					setSelected(-1,data);
					e.stopImmediatePropagation();
					e.preventDefault();
					return false;
				}
				if( e.keyCode == 13 ){//enter key
					var li = getCurrentSelected(data);
					if( li ){
						
						e.stopImmediatePropagation();
						e.preventDefault();
						hideList(data);					
						onUserSelected(li,data);
						return false;	
					}
					hideList(data);					
				}
				if( e.keyCode == 27 ){
					e.stopImmediatePropagation();
					e.preventDefault();
					return false;	
				}
			}
			switch( e.keyCode ){
				case 27:
					return true;
			}
			
			var text = getWords(data);
			//console.log("getWords return ",text);
			if( text != "" ){
				data.on.query(text,function(list){
					//console.log("got list = ",list);
					if( list.length ){
						showList(data,list,text);	
					}
					else{
						hideList(data);
					}
					
					
				});
			}
			else{
				hideList(data);
			}
		});
		
		
		
		$(data.ta).scroll(function(e){
				var ta = e.target;			
				var miror = $(data.clone);
				miror.get(0).scrollTop = ta.scrollTop;
			});
	}	
})(jQuery);
