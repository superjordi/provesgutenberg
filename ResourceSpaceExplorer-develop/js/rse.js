/*
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

*/

var media = wp.media;

// VIEW: MEDIA ITEM:

media.view.RSEItem = wp.Backbone.View.extend({

	tagName   : 'li',
	className : 'rse-item attachment',

	render: function() {

		this.template = media.template( 'rse-item-' + this.options.tab );
		this.$el.html( this.template( this.model.toJSON() ) );

		return this;

	}

});

// VIEW - BOTTOM TOOLBAR

media.view.Toolbar.RSE = media.view.Toolbar.extend({

	initialize: function() {

		_.defaults( this.options, {
			event : 'inserter',
			close : false,
			items : {
				// See wp.media.view.Button
				inserter     : {
					id       : 'rse-button',
					style    : 'primary',
					text     : resource_space_explorer.labels.insert,
					priority : 80,
					click    : function() {
						this.controller.state().rseInsert();
					}
				}
			}
		});

		media.view.Toolbar.prototype.initialize.apply( this, arguments );

		this.set( 'pagination', new media.view.Button({
			tagName: 'button',
			classes: 'rse-pagination button button-secondary',
			id: 'rse-loadmore',
			text: resource_space_explorer.labels.loadmore,
			priority: -20,
		}) );
	},

	refresh: function() {

		var selection = this.controller.state().props.get( '_all' ).get( 'selection' );

		// @TODO i think this is redundant
		this.get( 'inserter' ).model.set( 'disabled', !selection.length );

		media.view.Toolbar.prototype.refresh.apply( this, arguments );

	}

});

// VIEW - MEDIA CONTENT AREA

media.view.RSE = media.View.extend({

	events: {
		'click .rse-item-area'     : 'toggleSelectionHandler',
		'click .rse-item .check'   : 'removeSelectionHandler',
		'submit .rse-toolbar form' : 'updateInput'
	},

	initialize: function() {

		/* fired when you switch router tabs */
		
		var _this = this;

		this.collection = new Backbone.Collection();
		this.tab        = this.options.tab;

		this.createToolbar();
		this.clearItems();

		if ( this.model.get( 'items' ) ) {

			this.collection = new Backbone.Collection();
			this.collection.reset( this.model.get( 'items' ) );

			jQuery( '#rse-loadmore' ).attr( 'disabled', false ).show();
		} else {
			jQuery( '#rse-loadmore' ).hide();
		}

		this.collection.on( 'reset', this.render, this );

		this.model.on( 'change:params', this.changedParams, this );

		this.on( 'loading',       this.loading, this );
		this.on( 'loaded',        this.loaded, this );
		this.on( 'change:params', this.changedParams, this );
		this.on( 'change:page',   this.changedPage, this );

		jQuery( '.rse-pagination' ).click( function( event ) {
			_this.paginate( event );
		} );
		
		if ( _this.model.get( 'fetchOnRender' ) ) {
			_this.model.set( 'fetchOnRender', false );
			_this.fetchItems();
		}

	},

	render: function() {

		/* fired when you switch router tabs */

		var selection = this.getSelection();

		if ( this.collection && this.collection.models.length ) {

			this.clearItems();

			var container = document.createDocumentFragment();

			this.collection.each( function( model ) {
				container.appendChild( this.renderItem( model ) );
			}, this );

			this.$el.find( '.rse-items' ).append( container );

		}

		selection.each( function( model ) {
			var id = '#rse-item-' + this.tab + '-' + model.get( 'id' );
			this.$el.find( id ).closest( '.rse-item' ).addClass( 'selected details' );
		}, this );

		jQuery( '#rse-button' ).prop( 'disabled', !selection.length );

		return this;

	},

	renderItem : function( model ) {

		var view = new media.view.RSEItem({
			model   : model,
			tab     : this.tab
		});

		return view.render().el;

	},

	createToolbar: function() {

		// @TODO this could be a separate view:
		html = '<div class="rse-error attachments"></div>';
		this.$el.prepend( html );

		// @TODO this could be a separate view:
		html = '<div class="rse-empty attachments"></div>';
		this.$el.prepend( html );

		// @TODO this could be a separate view:
		html = '<ul class="rse-items attachments clearfix"></ul>';
		this.$el.append( html );

		// @TODO this could be a separate view:
		var toolbar_template = media.template( 'rse-search-' + this.tab );
		html = '<div class="rse-toolbar media-toolbar clearfix">' + toolbar_template( this.model.toJSON() ) + '</div>';
		this.$el.prepend( html );

	},

	removeSelectionHandler: function( event ) {

		var target = jQuery( '#' + event.currentTarget.id );
		var id     = target.attr( 'data-id' );

		this.removeFromSelection( target, id );

		event.preventDefault();

	},

	toggleSelectionHandler: function( event ) {

		if ( event.target.href )
			return;

		var target = jQuery( '#' + event.currentTarget.id );
		var id     = target.attr( 'data-id' );

		if ( this.getSelection().get( id ) )
			this.removeFromSelection( target, id );
		else
			this.addToSelection( target, id );

	},

	addToSelection: function( target, id ) {

		target.closest( '.rse-item' ).addClass( 'selected details' );

		this.getSelection().add( this.collection._byId[id] );

		// @TODO why isn't this triggered by the above line?
		this.controller.state().props.trigger( 'change:selection' );

	},

	removeFromSelection: function( target, id ) {

		target.closest( '.rse-item' ).removeClass( 'selected details' );

		this.getSelection().remove( this.collection._byId[id] );

		// @TODO why isn't this triggered by the above line?
		this.controller.state().props.trigger( 'change:selection' );

	},

	clearSelection: function() {
		this.getSelection().reset();
	},

	getSelection : function() {
		return this.controller.state().props.get( '_all' ).get( 'selection' );
	},

	clearItems: function() {

		this.$el.find( '.rse-item' ).removeClass( 'selected details' );
		this.$el.find( '.rse-items' ).empty();
		this.$el.find( '.rse-pagination' ).hide();

	},

	loading: function() {

		// show spinner
		this.$el.find( '.spinner' ).addClass( 'is-active' );

		// hide messages
		this.$el.find( '.rse-error' ).hide().text('');
		this.$el.find( '.rse-empty' ).hide().text('');

		// disable 'load more' button
		jQuery( '#rse-loadmore' ).attr( 'disabled', true );
	},

	loaded: function( response ) {

		// hide spinner
		this.$el.find( '.spinner' ).removeClass( 'is-active' );

	},

	fetchItems: function() {

		this.trigger( 'loading' );

		var data = {
			_nonce  : resource_space_explorer._nonce,
			tab     : this.tab,
			params  : this.model.get( 'params' ),
			page    : this.model.get( 'page' ),
			max_id  : this.model.get( 'max_id' )
		};

		media.ajax( 'resource_space_explorer_request', {
			context : this,
			success : this.fetchedSuccess,
			error   : this.fetchedError,
			data    : data
		} );

	},

	fetchedSuccess: function( response ) {
		console.info(response);
		if ( !this.model.get( 'page' ) ) {

			if ( !response.items ) {
				this.fetchedEmpty( response );
				return;
			}

			this.model.set( 'min_id', response.meta.min_id );
			this.model.set( 'items',  response.items );

			this.collection.reset( response.items );

		} else {

			if ( !response.items ) {
				this.moreEmpty( response );
				return;
			}

			this.model.set( 'items', this.model.get( 'items' ).concat( response.items ) );

			var collection = new Backbone.Collection( response.items );
			var container  = document.createDocumentFragment();

			this.collection.add( collection.models );

			collection.each( function( model ) {
				container.appendChild( this.renderItem( model ) );
			}, this );

			this.$el.find( '.rse-items' ).append( container );

		}
		
		// We disable the load more button when the last page as been loaded
		var disableLoadMore = response.meta && response.meta.page >= response.meta.total_pages;

		jQuery( '#rse-loadmore' ).attr( 'disabled', disableLoadMore ).show();
		this.model.set( 'max_id', response.meta.max_id );

		this.trigger( 'loaded loaded:success', response );

	},

	fetchedEmpty: function( response ) {

		this.$el.find( '.rse-empty' ).text( resource_space_explorer.labels.noresults ).show();
		this.$el.find( '.rse-pagination' ).hide();

		this.trigger( 'loaded loaded:noresults', response );

	},

	fetchedError: function( response ) {

		this.$el.find( '.rse-error' ).text( response.error_message ).show();
		jQuery( '#rse-loadmore' ).attr( 'disabled', false ).show();
		this.trigger( 'loaded loaded:error', response );

	},

	updateInput: function( event ) {

		// triggered when a search is submitted

		var params = this.model.get( 'params' );
		var els = this.$el.find( '.rse-toolbar' ).find( ':input' ).each( function( k, el ) {
			var n = jQuery(this).attr('name');
			if ( n )
				params[n] = jQuery(this).val();
		} );
		
		this.clearSelection();
		jQuery( '#rse-button' ).attr( 'disabled', 'disabled' );
		this.model.set( 'params', params );
		this.trigger( 'change:params' ); // why isn't this triggering automatically? might be because params is an object

		event.preventDefault();

	},

	paginate : function( event ) {

		if( 0 == this.collection.length )
			return;

		var page = this.model.get( 'page' ) || 1;

		this.model.set( 'page', page + 1 );
		this.trigger( 'change:page' );

		event.preventDefault();

	},

	changedPage: function() {

		// triggered when the pagination is changed

		this.fetchItems();

	},

	changedParams: function() {

		// triggered when the search parameters are changed

		this.model.set( 'page',   null );
		this.model.set( 'min_id', null );
		this.model.set( 'max_id', null );

		this.clearItems();
		this.fetchItems();

	}

});

// VIEW - MEDIA FRAME (MENU BAR)	

var post_frame = media.view.MediaFrame.Post;

media.view.MediaFrame.Post = post_frame.extend({

	initialize: function() {

		post_frame.prototype.initialize.apply( this, arguments );

		var id = 'rse';
		var controller = {
			id      : id,
			router  : id + '-router',
			toolbar : id + '-toolbar',
			menu    : 'default',
			title   : resource_space_explorer.labels.title,
			tabs    : resource_space_explorer.tabs,
			priority: 100 // places it above Insert From URL
		};

		for ( var tab in resource_space_explorer.tabs ) {

			// Content
			this.on( 'content:render:' + id + '-content-' + tab, _.bind( this.rseContentRender, this, tab ) );

			// Set the default tab
			if ( resource_space_explorer.tabs[tab].defaultTab )
				controller.content = id + '-content-' + tab;

		}

		this.states.add([
			new media.controller.RSE( controller )
		]);

		// Tabs
		this.on( 'router:create:' + id + '-router', this.createRouter, this );
		this.on( 'router:render:' + id + '-router', _.bind( this.rseRouterRender, this ) );

		// Toolbar
		this.on( 'toolbar:create:' + id + '-toolbar', this.rseToolbarCreate, this );
		//this.on( 'toolbar:render:' + id + '-toolbar', _.bind( this.rseToolbarRender, this ) );

	},

	rseRouterRender : function( view ) {

		var id   = 'rse';
		var tabs = {};

		for ( var tab in resource_space_explorer.tabs ) {
			tab_id = id + '-content-' + tab;
			tabs[tab_id] = {
				text : resource_space_explorer.tabs[tab].text
			};
		}

		view.set( tabs );

	},

	rseToolbarRender : function( view ) {

		view.set( 'selection', new media.view.Selection.RSE({
			controller : this,
			collection : this.state().props.get('_all').get('selection'),
			priority   : -40
		}).render() );

	},

	rseContentRender : function( tab ) {

		/* called when a tab becomes active */

		this.content.set( new media.view.RSE( {
			controller : this,
			model      : this.state().props.get( tab ),
			tab        : tab,
			className  : 'clearfix attachments-browser rse-content rse-content-' + tab
		} ) );

	},

	rseToolbarCreate : function( toolbar ) {

		toolbar.view = new media.view.Toolbar.RSE( {
			controller : this
		} );

	}

});

// CONTROLLER:

media.controller.RSE = media.controller.State.extend({

	initialize: function( options ) {

		this.props = new Backbone.Collection();

		for ( var tab in options.tabs ) {
			this.props.add( new Backbone.Model({
				id     : tab,
				params : {},
				page   : null,
				min_id : null,
				max_id : null,
				fetchOnRender : options.tabs[ tab ].fetchOnRender,
			}) );

		}

		this.props.add( new Backbone.Model({
			id        : '_all',
			selection : new Backbone.Collection()
		}) );

		this.props.on( 'change:selection', this.refresh, this );

	},

	refresh: function() {
		this.frame.toolbar.get().refresh();
	},

	rseInsert: function() {

		var self = this, $button, $spinner, selection, attachments, complete, toggleLoading, insertImages, doItem;

		if ( this.inserting ) {
			return;
		}

		this.inserting = true;

		// Only use this custom insert function for resource space.
		if ( this.id !== "rse" ) {
			controller.rseInsert();
		}

		$button     = jQuery( '#rse-button' );
		$spinner    = $button.parent().find('.spinner');
		selection   = self.frame.content.get().getSelection();
		attachments = [];

		if ( ! $spinner.length ) {
			$spinner = jQuery( '<span/>', { 'class': 'spinner', 'style': 'margin: 20px 0 20px 10px; float: left;' } );
			$spinner.insertBefore( $button );
		}

		complete = function() {
			toggleLoading( false );
			selection.reset();
			self.frame.close();
			delete self.inserting;
			jQuery( '.rse-content-resource-space-all .selected').removeClass( 'selected' );
			jQuery( '.rse-content-resource-space-all .details').removeClass( 'details' );
		}

		toggleLoading = function( enable ) {
			$spinner.toggleClass( 'is-active', enable );
			$button.attr( 'disabled', enable );
			jQuery('.rse-items').toggleClass( 'resourcespace-loading', enable );
		}

		/**
		 * Insert the images.
		 * Create image HTML and insert into currently active editor.
		 *
		 * @return null
		 */
		insertImages = function() {

			// For storing array of image HTML.
			var images = [];

			// Loop through attachments and build image element HTML.
			_.each( this.attachments, function( attachment ) {

				var $img = jQuery('<img />');
				var size = 'full';

				if ( attachment.sizes[ size ] ) {
					$img.attr( 'src', attachment.sizes[ size ].url );
					$img.attr( 'width', attachment.sizes[ size ].width );
					$img.attr( 'height', attachment.sizes[ size ].height );
				} else {
					$img.attr( 'src', attachment.url );
					$img.attr( 'width', attachment.width );
					$img.attr( 'height', attachment.height );
				}

				$img.attr( 'alt', attachment.title );

				$img.addClass( 'alignnone' );
				$img.addClass( 'size-' + size );
				$img.addClass( 'wp-image-' + attachment.id );

				images.push( $img.prop('outerHTML') );

			});

			// Insert all HTML in one go.
			if ( typeof( tinymce ) === 'undefined' || tinymce.activeEditor === null || tinymce.activeEditor.isHidden() ) {
				media.editor.insert( images.join( "\n\n" ) + "\n\n" );
			} else {
				media.editor.insert( "<p>" + images.join( "</p><p>" ) + "</p>" );
			}

			this.complete();

		}

		/**
		 * Proccess a single selection item.
		 * Fetch the
		 * @param
		 * @return {[type]}       [description]
		 */
		doItem = function( model ) {

			jQuery.post( ajaxurl, {
				action:      'resource_space_explorer_get_resource',
				resource_id: model.get( 'id' ),
				post:        parseInt( jQuery('#post_ID').val() ),
			}).done( function( response ) {

				if ( ! response.success ) {

					if ( 'data' in response ) {
						alert( response.data );
					} else {
						alert( 'Failed to import image.' );
					}

					toggleLoading( false );

					return;
				}

				attachments.push( response.data );

			}).always( function() {

				if ( attachments.length >= selection.length ) {

					var callback = insertImages;

					// Allow overriding insert callback.
					if ( self.frame.options.resourceSpaceInsertCallback ) {
						callback = self.frame.options.resourceSpaceInsertCallback
					}

					callback = _.bind( callback, { attachments: attachments, complete: complete } );
					callback();

				}

			}).fail( function( response ) {
				alert( 'There was a problem importing your image.' );
			} );

		}

		toggleLoading( true );
		selection.each( doItem );

	},

});

// For featured image
(function ( $ ) {

	var resourceSpaceFeaturedImage = function() {

		var controller = wp.media.controller.FeaturedImage;

		var resourceSpaceFrame = null;

		wp.media.controller.FeaturedImage = controller.extend({

			initialize: function() {
				wp.media.controller.FeaturedImage.__super__.initialize.apply( this );
				this.initResourceSpaceFrame();
			},

			activate: function() {

				wp.media.controller.FeaturedImage.__super__.activate.apply( this, arguments );

				// Add the stock images tab.
				this.frame.on( 'router:render:browse', this.resourceSpaceCreateTab );
				this.frame.on( 'content:render:resourceSpace', this.resourceSpaceRenderTab );

			},

			resourceSpaceCreateTab: function( routerView ) {

				routerView.set({
					resourceSpace: {
						text:     'Stock Images',
						priority: 60
					}
				});

				routerView.controller.content.mode('browse');

			},

			resourceSpaceRenderTab: function() {

				if ( resourceSpaceFrame ) {
					resourceSpaceFrame.open();
					return;
				}

				resourceSpaceFrame = wp.media.frames.resourceSpaceFeaturedImageFrame;

				resourceSpaceFrame.on( 'open', function() {

					// Switch content view back to browse in the original frame.
					var routerView = wp.media.frame.views.get( '.media-frame-router' )[0];
					routerView.controller.content.mode('browse');

					window.setTimeout( function() {

						// Ensure that the resource space frame is on top.
						resourceSpaceFrame.$el.closest('.media-modal').parent().appendTo( 'body' );

						// Hide all other menu options in the frame.
						resourceSpaceFrame.$el.addClass( 'hide-menu' );

					}, 1 );

					// Slightly hcky workaround because for some reason the load more
					// button doesn't exist when the event callback is attached.
					// TODO: doesn't work
					$('#rse-loadmore').on('click', function(e) {
						var view = resourceSpaceFrame.views.get('.media-frame-content' );
						if ( view.length ) {
							view[0].paginate(e);
						}
					} );

				} )

				resourceSpaceFrame.open();

			},

			initResourceSpaceFrame: function() {

				if ( ! wp.media.frames.resourceSpaceFeaturedImageFrame ) {

					var self = this;

					// Bit of an odd hack. But we have to set this to a non-falsey value
					// in order to prevent infinite loop when creating the new frame.
					wp.media.frames.resourceSpaceFeaturedImageFrame = 1;

					wp.media.frames.resourceSpaceFeaturedImageFrame = wp.media({
						frame : "post",
						state : 'rse',
						resourceSpaceInsertCallback: function() {
							var selection   = self.get( 'selection' );
							var attachments = ( this.attachments.length ) ? [ this.attachments[0] ] : [];
							selection.reset( attachments );
							this.complete();

							if ( attachments.length ) {
								wp.media.featuredImage.set( attachments[0].id );
							}

							wp.media.frame.close();
						},
					});
				}

			}

		} );

	}

	$(document).ready( function() {
		resourceSpaceFeaturedImage()
	} );

}( jQuery ));
