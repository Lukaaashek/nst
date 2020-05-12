<?php

namespace App\Model;

use Nette;


/**
 * Articles rating management.
 */
final class ArticlesRating
{
	use Nette\SmartObject;

	const
		TABLE_NAME = 'articles_rating',
		COLUMN_ID = 'id',
		COLUMN_ARTICLES_ID = 'articles_id',
		COLUMN_USERS_ID = 'users_id',
		COLUMN_RATING = 'rating';


	/** @var Nette\Database\Context */
	private $database;
	
	/** @var Articles */
	private $articles;


	public function __construct(Nette\Database\Context $database, Articles $articles)
	{
		$this->database = $database;
		$this->articles = $articles;
	}	
	
	/**
	 * Check if article is rated by user.
	 * @return bool
	 */
	public function isArticleRatedByUser(int $articleId, int $userId)
	{
		$articleRating = $this->database->table(self::TABLE_NAME)
				->where(self::COLUMN_ARTICLES_ID, $articleId)
				->where(self::COLUMN_USERS_ID, $userId)
				->count();
		return $articleRating > 0;
	}
	
	/**
	 * Add article rating.
	 * @return int $totalRating
	 */
	public function add(int $articleId, int $userId, int $ratingValue)
	{
		$olderRatings = $this->database->table(self::TABLE_NAME)
				->where(self::COLUMN_ARTICLES_ID, $articleId)
				->fetchPairs(self::COLUMN_ID, self::COLUMN_RATING);
		
		$totalRating = 0;
		foreach ($olderRatings as $rating) {
			$totalRating += $rating;
		}
		$totalRating += $ratingValue;
		
		
		$articleRating = $this->database->table(self::TABLE_NAME)
			->insert([
				self::COLUMN_ARTICLES_ID => $articleId,
				self::COLUMN_USERS_ID => $userId,
				self::COLUMN_RATING => $ratingValue,
			]);
		
		$article = $this->articles->getById($articleId);
		$article->update([self::COLUMN_RATING => $totalRating]);
		
		return $totalRating;
	}
	
	/**
	 * Get all user ratings.
	 * @return bool
	 */
	public function getAllUserRatings(int $userId)
	{
		$articlesRatings = $this->database->table(self::TABLE_NAME)
				->where(self::COLUMN_USERS_ID, $userId)
				->fetchPairs(self::COLUMN_ARTICLES_ID, self::COLUMN_RATING);
		return $articlesRatings;
	}
}