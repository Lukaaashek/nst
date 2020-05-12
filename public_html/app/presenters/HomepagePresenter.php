<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Model;
use Nette;
use Nette\Application\Responses\JsonResponse;

final class HomepagePresenter extends BasePresenter
{
	/** @var Model\Articles */
	private $articles;
	
	/** @var Model\ArticlesRating */
	private $articlesRating;
	
	private $itemsPerPage = 5;
	
	/** @persistent string */
	public $sort;

	public function __construct(Model\Articles $articles, Model\ArticlesRating $articlesRating)
	{
		$this->articles = $articles;
		$this->articlesRating = $articlesRating;
	}
	
	public function renderDefault($page = 1, $sort = '')
	{
		$sortOptions = $this->articles->getSortOptions();
		$order = !empty($sort) && $sortOptions[$sort] ? $sortOptions[$sort] : 'id';
		
		$this->template->sort = $this->sort = $sort;
		
		if ($this->getUser()->isLoggedIn()) {
			$articles = $this->articles->getAll($order);
		} else {
			$articles = $this->articles->getArticlesVisibleToUnsigned($order);
		}
		
		// Pagination
		$lastPage = 0;
		$this->template->articles = $articles->page($page, $this->itemsPerPage, $lastPage);
		
		$this->template->page = $page;
		$this->template->lastPage = $lastPage;
		
		if ($this->user->id) {
			$this->template->userRating = $this->articlesRating->getAllUserRatings($this->user->id);
		} else {
			$this->template->userRating = [];
		}
	}
	
	public function handleRate($articleId, $rating) {
		
		if (!$this->isAjax()) {
			die('Chyba');
		}
		
		
		if (!$this->getUser()->isLoggedIn()) {
			$this->sendResponse(new JsonResponse([
				'result' => 'error', 
				'message' => 'Nepřihlášení uživatelé nemohou hodnotit!'
			]));
		}
		
		if (!$articleId) {
			$this->sendResponse(new JsonResponse([
				'result' => 'error', 
				'message' => 'Chybí ID článku!'
			]));
		}
		
		
		if ($rating != 'like' && $rating != 'dislike') {
			$this->sendResponse(new JsonResponse([
				'result' => 'error', 
				'message' => 'Chybná hodnota hodnocení!'
			]));
		}
		
		$article = $this->articles->getById((int) $articleId);
		if (!$article) {
			$this->sendResponse(new JsonResponse([
				'result' => 'error', 
				'message' => 'Článek neexistuje!'
			]));
		}
		
		$ratedByUser = $this->articlesRating->isArticleRatedByUser((int) $articleId, $this->user->id);
		if($ratedByUser) {
			$this->sendResponse(new JsonResponse([
				'result' => 'error', 
				'message' => 'Tento článek už jste hodnotili!'
			]));
		}
		
		$ratingValue = $rating == 'like' ? 1 : -1;
		$totalRating = $this->articlesRating->add((int) $articleId, $this->user->id, $ratingValue);

		if (is_int($totalRating)) {
			$this->sendResponse(new JsonResponse([
				'result' => 'success', 
				'message' => 'Úspěšně ohodnoceno!',
				'total' => $totalRating,
			]));
		} else {
			$this->sendResponse(new JsonResponse([
				'result' => 'error', 
				'message' => 'Někde se stala chyba!'
			]));
		}
	}
}
