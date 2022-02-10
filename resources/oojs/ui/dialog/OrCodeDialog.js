bs.ui.dialog.QrCodeDialog = function ( config ) {
	if ( !config.hasOwnProperty( 'callback' ) ) {
		config.callback = {};
	}
	bs.ui.dialog.QrCodeDialog.super.call( this, config );

	this.name = config.name || 'QrCodeDialog';
	this.id = config.id || 'bs-qr-code-dlg';
	this.data = config.data || '';
	var altMsg = config.altMsg;
	var page = config.page || '';
	if ( altMsg !== '' ) {
		var alt = mw.message( altMsg, page ).text();
		this.alt = ' alt="' + alt + '"';
	}

	this.$element.attr( 'id', this.id );

	bs.ui.dialog.SimpleMessageDialog.prototype.makeActionAccept = function () {
		var params = {
			action: 'ok',
			label: mw.message( 'ooui-dialog-message-accept' ).plain()
		};
		if ( this.hasOwnProperty( 'id' ) ) {
			params.id = this.id + '-btn-ok';
		}
		return params;
	};

	bs.ui.dialog.QrCodeDialog.static.actions = [
		bs.ui.dialog.SimpleMessageDialog.prototype.makeActionAccept.call( this )
	];
};

OO.inheritClass( bs.ui.dialog.QrCodeDialog, bs.ui.dialog.SimpleMessageDialog );

bs.ui.dialog.QrCodeDialog.prototype.initialize = function () {
	bs.ui.dialog.QrCodeDialog.super.prototype.initialize.call( this );

	this.content = new OO.ui.PanelLayout( { padded: true, expanded: false } );
	if ( this.data !== '' ) {
		this.content.$element.append(
			'<div style="width: 100%; display:flex; justify-content: space-around;"><img src="' + this.data + '"' + this.alt + '/></div>'
		);
		this.text.$element.append( this.content.$element );
	}
};
