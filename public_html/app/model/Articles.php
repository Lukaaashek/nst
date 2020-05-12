<?php

namespace App\Model;

use Nette;


/**
 * Articles management.
 */
final class Articles
{
	use Nette\SmartObject;

	const
		TABLE_NAME = 'articles',
		COLUMN_ID = 'id',
		COLUMN_TITLE = 'title',
		COLUMN_PEREX = 'perex',
		COLUMN_CREATE_DATE = 'create_date',
		COLUMN_SIGNED_ONLY = 'signed_only',
		COLUMN_RATING = 'rating';


	/** @var Nette\Database\Context */
	private $database;


	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}


	/**
	 * Get all articles.
	 * @return Nette\Database\Table\Selection
	 */
	public function getAll(string $order = 'id')
	{
		$articles = $this->database->table(self::TABLE_NAME)->order($order);
		return $articles;
	}
	
	/**
	 * Get all articles.
	 * @return Nette\Database\Table\Selection
	 */
	public function getArticlesVisibleToUnsigned(string $order = 'id')
	{
		$articles = $this->database->table(self::TABLE_NAME)
				->where(self::COLUMN_SIGNED_ONLY, 0)
				->order($order);
		return $articles;
	}
	
	/**
	 * Get sort options.
	 * @return array $sortOptions
	 */
	public function getSortOptions()
	{
		$sortOptions = [
			'newest' => self::COLUMN_CREATE_DATE . ' DESC',
			'oldest' => self::COLUMN_CREATE_DATE . ' ASC',
			'title_a' => self::COLUMN_TITLE . ' ASC',
			'title_z' => self::COLUMN_TITLE . ' DESC',
			'worst' => self::COLUMN_RATING . ' ASC',
			'best' => self::COLUMN_RATING . ' DESC',
		];
		return $sortOptions;
	}
	
	
	/**
	 * Get article by ID.
	 * @return Nette\Database\Table\Selection
	 */
	public function getById(int $id)
	{
		$article = $this->database->table(self::TABLE_NAME)->get($id);
		return $article;
	}
}