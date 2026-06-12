<?php

namespace BlueSpice\QrCode\DynamicFileDispatcher;

use MediaWiki\Permissions\Authority;
use MediaWiki\Title\TitleFactory;
use MWStake\MediaWiki\Component\DynamicFileDispatcher\IDynamicFile;
use MWStake\MediaWiki\Component\DynamicFileDispatcher\IDynamicFileModule;
use MWStake\MediaWiki\Component\FileStorageUtilities\StorageHandler;
use RepoGroup;

class QrCode implements IDynamicFileModule {

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

	public function getFile( array $params ): ?IDynamicFile {
		return new QrCodeImage(
			$this->titleFactory,
			$params['pagename'] ?? $this->titleFactory->newMainPage()->getPrefixedText(),
			$params['query'] ?? '',
			isset( $params['size'] ) ? (int)$params['size'] : 100,
			$this->repoGroup,
			$this->storageHandler
		);
	}

	/**
	 * @inheritDoc
	 */
	public function isAuthorized( Authority $user, array $params ): bool {
		return true;
	}
}
