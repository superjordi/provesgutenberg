(function( window, $ ){


	var view = media.view.MEXP

	view = view.extend( {

		noMorePosts: false,

		fetchedSuccess: function( response ) {

			console.info(response);
			media.view.MEXP.__super__.fetchedSuccess.apply( this, [response] );
			
			console.info(response.meta);
			
			if ( response.meta && 'page' in response.meta && 'total_pages' in response.meta  ) {
				if ( response.meta.page >= response.meta.total_pages  ) {
					this.noMorePosts = true;
					jQuery( '#' + this.service.id + '-loadmore' ).attr( 'disabled', true );
				}
			}

		},

		/**
		 * Fix bug in MEXP plugin.
		 * Pagination disabled attr has no effect other than visual.
		 * https://github.com/Automattic/media-explorer/pull/67
		 */
		paginate : function( event ) {

			if ( this.noMorePosts ) {
				return;
			}

			media.view.MEXP.__super__.paginate.apply( this, [event] );

		},


	} );

})( window, this.jQuery );
