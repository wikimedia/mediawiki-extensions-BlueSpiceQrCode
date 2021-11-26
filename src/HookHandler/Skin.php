<?php

namespace BlueSpice\QrCode\HookHandler;

use Action;
use MediaWiki\Hook\SkinTemplateNavigation__UniversalHook;
use SkinTemplate;

class Skin implements SkinTemplateNavigation__UniversalHook {
	/**
	 * @param SkinTemplate $sktemplate
	 * @param array &$links
	 * @return void
	 */
	public function onSkinTemplateNavigation__Universal( $sktemplate, &$links ) : void {
		/**
		 * Unfortunately the `VectorTemplateTest::testGetMenuProps` from `Skin:Vector` will break
		 * in `REL1_35`, as it does not properly clear out all hook handlers.
		 * See https://github.com/wikimedia/Vector/blob/1b03bafb1267f350ee2b0018da53c31ee0674f92/tests/phpunit/integration/VectorTemplateTest.php#L107-L108
		 * In later versions this test does not exist anymore and we can remove the bail out again.
		 * We do not perform any own UnitTests on this class, so bailing out here should be fine.
		 */
		if ( defined( 'MW_PHPUNIT_TEST' ) ) {
			return;
		}

		if ( $sktemplate->skinname === 'bluespicecalumma' ) {
			// BlueSpiceCalumma has its own integration see:
			// BlueSpice\QrCode\Hook\ChameleonSkinTemplateOutputPageBeforeExec\\AddQrCode
			return;
		}

		$title = $sktemplate->getSkin()->getTitle();
		if ( ( $title->getNamespace() === NS_SPECIAL )
			|| ( Action::getActionName( $sktemplate->getSkin() ) !== 'view' ) ) {
			return;
		}

		$links['actions'] = array_merge(
			$links['actions'],
			[
				'bs-qr-code' => [
					'href' => '',
					'title' => $sktemplate->msg( 'bs-qr-code-action-qr-code-title' )->text(),
					'text' => $sktemplate->msg( 'bs-qr-code-action-qr-code-text' )->text()
				]
			]
		);
	}
}
