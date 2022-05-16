<?php

namespace App\Models;
use CodeIgniter\Model;

class CommentsModel extends Model {

  protected $db;
  protected $table = 'comments';
  protected const COMMENTS_PER_PAGE = 3;

  public function __construct() 
  {
    $this->db = \Config\Database::connect();
  }

  // Вспомогательный метод, определящий в каком состоянии сейчас сортировки.
  public function getSortParams($request) {
    $sortType = 'sortDate';
    $sortValue = 'DESC';
    if ($request->getVar("sortId")) {
      $sortType = "sortId";
      $sortValue = $request->getVar("sortId");
    } else if ($request->getVar("sortDate")) {
      $sortType = "sortDate";
      $sortValue = $request->getVar("sortDate");
    } 

    if (! $request->getVar("page") ) {
      $pageNumber = 1;
    } else {
      $pageNumber = $request->getVar('page');
    }

    return [
      'sortValue' => $sortValue,
      'sortType' => $sortType,
      'pageNumber' => $pageNumber
    ];
  }

  // Возврашает 3 комментария, учитывая на какой 
  // странице сейчас пользователь
  // и какие сортировки применены
  public function getComments($currentPageNumber, $sortType, $sortValue) {
    if ($sortType === 'sortDate') {
      $orderBy = "date";
    } elseif ($sortType === 'sortId') {
      $orderBy = "id";
    }

    $offset = ($currentPageNumber - 1) * self::COMMENTS_PER_PAGE;

    $builder = $this->db->table($this->table)->select()->orderBy($orderBy, $sortValue);
    $result = $builder->get(self::COMMENTS_PER_PAGE, $offset);

    return $result->getResultArray();
  }

  // Сохраняет комментарий в базу данных и возвращает id последней сохраненной записи
  public function store($commentData) {
    $this->db->table($this->table)->insert($commentData);
    $resultId = $this->db->insertID();
    return $resultId;
  }
  
  // Удаляет комментарий из базы данных по id и возврашает число затронутых строк
  public function deleteComment($commentId) {
    $builder = $this->db->table($this->table)->delete(['id' => $commentId]);
    $result = $this->db->affectedRows();
    return $result; 
  }
  
  // Вспомогательный метод, рассчитывает сколько сейчас должно быть страниц для пагинации
  public function getTotalNumberOfPages() {
    return ceil ($this->db->table($this->table)->select()->countAll() / self::COMMENTS_PER_PAGE)  ;
  }
}