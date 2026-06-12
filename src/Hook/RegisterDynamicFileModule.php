<?php

namespace BlueSpice\QrCode\Hook;

use BlueSpice\QrCode\DynamicFileDispatcher\QrCode;
use MediaWiki\Title\TitleFactory;
use MWStake\MediaWiki\Component\DynamicFileDispatcher\MWStakeDynamicFileDispatcherRegisterModuleHook;
use MWStake\MediaWiki\Component\FileStorageUtilities\StorageHandler;
use RepoGroup;

class RegisterDynamicFileModule implements MWStakeDynamicFileDispatcherRegisterModuleHook {

	/** @var TitleFactory */
	private $titleFactory;

	/** @var RepoGroup */
	private $repoGroup;

	/** @var StorageHandler */
	private $storageHandler;

	/**
	 * @param TitleFactory $titleFactory
	 * @param RepoGroup $repoGroup
	 * @param StorageHandler $storageHandler
	 */
	public function __construct( TitleFactory $titleFactory, RepoGroup $repoGroup, StorageHandler $storageHandler ) {
		$this->titleFactory = $titleFactory;
		$this->repoGroup = $repoGroup;
		$this->storageHandler = $storageHandler;
	}

	/**
	 * @inheritDoc
	 */
	public function onMWStakeDynamicFileDispatcherRegisterModule( &$modules ) {
		$modules['qrcode'] = new QrCode( $this->titleFactory, $this->repoGroup, $this->storageHandler );
	}
}
