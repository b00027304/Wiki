(function( $ ) {
	// currentFocus is used to determine where to insert tags
	// var currentFocused = $( '#wpTextbox1' );
	
	mw.toolbar = {
		$toolbar : $( '#toolbar' ),
		buttons : [],
		// If you want to add buttons, use 
		// mw.toolbar.addButton( imageFile, speedTip, tagOpen, tagClose, sampleText, imageId, selectText );
		addButton : function() {
			this.buttons.push( [].slice.call( arguments ) );
		},
		insertButton : function( imageFile, speedTip,imageId ) {
			var image = $('<img>', {
				width  : 160,
				height : 22,
				src    : imageFile,
				alt    : speedTip,
				title  : speedTip,
				id     : imageId || '',
				'class': 'mw-toolbar-editbutton'
			} ).click( function() {
				mw.toolbar.insertItemtype( imageId );
				return false;
			} );

			this.$toolbar.append( image );
			return true;
		},

		insertItemtype : function( Itemtype) { 
			 
		 var exists = /itemtp/g.test(window.location.href);
         if(!exists){window.location = document.URL+'&itemtp='+Itemtype;}
         else {
        	 var position=window.location.href.search(/itemtp/);
        	 var chposition=position+'itemtp'.length+1;
        	 var newurl=window.location.href.substring(0,chposition)+Itemtype; 
        	 window.location=newurl;
        	 }
		 $('#itemtp').val(Itemtype);
		}, 
		
		init : function() {
			// Legacy
			// Merge buttons from mwCustomEditButtons
			var buttons = [].concat( this.buttons, window.mwCustomEditButtons );
			for ( var i = 0; i < buttons.length; i++ ) {
				if ( $.isArray( buttons[i] ) ) {
					// Passes our button array as arguments
					mw.toolbar.insertButton.apply( this, buttons[i] );
				} else {
					// Legacy mwCustomEditButtons is an object
					var c = buttons[i];
					mw.toolbar.insertButton( c.imageFile, c.speedTip, c.tagOpen, c.tagClose, c.sampleText, c.imageId, c.selectText );
				}
			}
			return true;
		},
		
		inArray: function (needle, haystack) {
		    var length = haystack.length;
		    for(var i = 0; i < length; i++) {
		        if(haystack[i] == needle) return true;
		    }
		    return false;
		}
		
		
	};

	//Legacy
	window.addButton =  mw.toolbar.addButton;
	window.insertTags = mw.toolbar.insertTags;

	//make sure edit summary does not exceed byte limit
	$( '#wpSummary' ).byteLimit( 250 );
	
	$( document ).ready( function() {
		/**
		 * Restore the edit box scroll state following a preview operation,
		 * and set up a form submission handler to remember this state
		 */
		var scrollEditBox = function() {
			var editBox = document.getElementById( 'wpTextbox1' );
			var scrollTop = document.getElementById( 'wpScrolltop' );
			var $editForm = $( '#editform' );
			if( $editForm.length && editBox && scrollTop ) {
				if( scrollTop.value ) {
					editBox.scrollTop = scrollTop.value;
				}
				$editForm.submit( function() {
					scrollTop.value = editBox.scrollTop;
				});
			}
		};
		scrollEditBox();
		
		// Create button bar
		mw.toolbar.init();
		
		$( 'textarea, input:text' ).focus( function() {
			currentFocused = $(this);
		});

		// HACK: make currentFocused work with the usability iframe
		// With proper focus detection support (HTML 5!) this'll be much cleaner
		var iframe = $( '.wikiEditor-ui-text iframe' );
		if ( iframe.length > 0 ) {
			$( iframe.get( 0 ).contentWindow.document )
				.add( iframe.get( 0 ).contentWindow.document.body ) // for IE
				.focus( function() { currentFocused = iframe; } );
		}
	});
})(jQuery);
