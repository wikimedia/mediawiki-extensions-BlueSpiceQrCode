<?php

namespace BlueSpice\QrCode\DynamicFileDispatcher;

use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Exception;
use File;
use MediaWiki\Message\Message;
use MediaWiki\Rest\Stream;
use MediaWiki\Status\Status;
use MediaWiki\Title\TitleFactory;
use MWStake\MediaWiki\Component\DynamicFileDispatcher\IDynamicFile;
use MWStake\MediaWiki\Component\FileStorageUtilities\StorageHandler;
use Psr\Http\Message\StreamInterface;
use RepoGroup;

class QrCodeImage implements IDynamicFile {

	/** @var TitleFactory */
	private $titleFactory;

	/** @var string */
	private $pagename;

	/** @var string */
	private $query;

	/** @var int */
	private $size;

	/** @var RepoGroup */
	private $repoGroup;

	/** @var StorageHandler */
	private $storageHandler;

	/**
	 * @param TitleFactory $titleFactory
	 * @param string $pagename
	 * @param string $query
	 * @param int $size
	 * @param RepoGroup $repoGroup
	 * @param StorageHandler $storageHandler
	 */
	public function __construct(
		TitleFactory $titleFactory,
		string $pagename,
		string $query,
		int $size,
		RepoGroup $repoGroup,
		StorageHandler $storageHandler
	) {
		$this->titleFactory = $titleFactory;
		$this->pagename = $pagename;
		$this->query = $query;
		$this->size = $size;
		$this->repoGroup = $repoGroup;
		$this->storageHandler = $storageHandler;
	}

	/**
	 * @inheritDoc
	 */
	public function getMimeType(): string {
		return 'image/png';
	}

	/**
	 * @inheritDoc
	 */
	public function getStream(): StreamInterface {
		[ $status, $file ] = $this->generate();
		if ( !$status->isGood() || !$file ) {
			$messages = $status->getMessages( null );
			$errorMessage = [];
			foreach ( $messages as $message ) {
				$errorMessage[] = Message::newFromSpecifier( $message )->text();
			}
			throw new Exception(
				'FATAL: QrCode could not be saved! ' . implode( ', ', $errorMessage )
			);
		}
		return new Stream( fopen( $file->getLocalRefPath(), 'rb' ) );
	}

	/**
	 * @return array
	 */
	public function generate(): array {
		$repo = $this->repoGroup->getRepoByName( 'QrCode' );
		$id = md5( $this->pagename . $this->query . $this->size );
		$filename = ucfirst( $id . ".png" );
		$file = $repo->newFile( $filename );

		if ( $file instanceof File && $file->exists() ) {
			return [ Status::newGood(), $file ];
		}
		$title = $this->titleFactory->newFromText( $this->pagename );
		$url = $title->getFullURL( $this->query );

		$qrCode = new QrCode(
			$url,
			new Encoding( 'UTF-8' ),
			ErrorCorrectionLevel::Low,
			$this->size
		);

		$writer = new PngWriter();
		$result = $writer->write( $qrCode );
		$qrCodeSrc = $result->getString();

		$status = $this->storageHandler->newTransaction()
			->create( $file->getName(), $qrCodeSrc, 'QrCode', [ 'overwrite' => true ] )
			->commit();

		$file = $repo->newFile( $filename );
		return [ $status, $file ];
	}
}
