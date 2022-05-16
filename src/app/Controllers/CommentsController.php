<?php

namespace App\Controllers;
use App\Models\CommentsModel;

class CommentsController extends BaseController
{
  protected $commentsModel;

  protected const DEFAULT_PAGE_NUMBER = 1;

  public function __construct()
  {
    $this->commentsModel = new CommentsModel();
    $this->validator = \Config\Services::validation();
  }

	public function index()
	{
		return view('comments');
	}

	public function show()
	{
    $sortParams = $this->commentsModel->getSortParams($this->request);

    // Получаем различные параметры для отображения.
    $data['comments'] = $this->commentsModel->getComments($sortParams['pageNumber'], $sortParams['sortType'], $sortParams['sortValue']);
    $data['currentPageNumber'] = $sortParams['pageNumber'];
    $data['totalNumberOfPages'] = $this->commentsModel->getTotalNumberOfPages();
    $data['sort']['sortType'] = $sortParams['sortType'];
    $data['sort']['sortValue'] = $sortParams['sortValue'];

    return view('comments_list', $data);
	}

  public function store() {

    $sortParams = $this->commentsModel->getSortParams($this->request);
    
    //Валидация
		if($this->request->getPost()) {
			$this->validator->setRules([
				'name' => ['label' => 'Имя', 'rules' => 'required|valid_email|max_length[150]'],
				'text' => ['label' => 'Текст комментария', 'rules' => 'required|max_length[1000]'],
				'date' => ['label' => 'Дата комментария', 'rules' => 'required|valid_date'],
			],
      [   // Локализация сообщений ошибок
        'name' => [
            'required' => 'Пожалуйста введите свой E-mail.',
            'valid_email' => 'Пожалуйста введите свой E-mail в правильном формате.',
            'max_length' => 'К сожалению использование E-mail длиннее 150 символов невозможно.'
        ],
        'text' => [
            'required' => 'Пожалуйста укажите желаемую дату комментария.',
            'valid_date' => 'Пожалуйста укажите дату в правильном формате.',
        ],
      ]
      );  
			
      // Если нет ошибок при валидации, сохраняем в бд
			if (!$this->validator->withRequest($this->request)->run()) {
				$data['errors'] = $this->validator->getErrors();
			} else {
				$resultId = $this->commentsModel->store([
          "name" => $this->request->getPost("name"),
          "text" => $this->request->getPost("text"),
          "date" => $this->request->getPost("date")
        ]);
        
        // Возвращаем 3 комментария которые теперь должны отображаться на странице
        $data['comments'] =$this->commentsModel->getComments($sortParams['pageNumber'], $sortParams['sortType'], $sortParams['sortValue']);
        $data['currentPageNumber'] = $sortParams['pageNumber'];
        $data['totalNumberOfPages'] = $this->commentsModel->getTotalNumberOfPages();
			}
		} else {
			$data['errors'] = "Запрос был отправлен неправильным методом.";
		}

    $response = json_encode($data);
    header('Content-Type: application/json');
    echo $response;
    exit();
  }

  public function delete($id) {
    $data['result'] = $this->commentsModel->deleteComment($id);

    $sortParams = $this->commentsModel->getSortParams($this->request);

    // Возвращаем новые комментарии для страницы на который находится пользователь
    $data['comments'] =$this->commentsModel->getComments($sortParams['pageNumber'], $sortParams['sortType'], $sortParams['sortValue']);
    $data['currentPageNumber'] = $sortParams['pageNumber'];
    $data['totalNumberOfPages'] = $this->commentsModel->getTotalNumberOfPages();

    $response = json_encode($data);
    header('Content-Type: application/json');
    echo $response;
    exit();
  } 
}
