<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Тестовое задание</title>
	<meta name="description" content="The small framework with powerful features">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" type="image/png" href="/favicon.ico"/>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
	
  <link rel="stylesheet" href="/css/style.css"/>

</head>
<body>

<div class="alert-box">
  <div class="container">
    <p class="alert-box__message text-center">
    </p>
  </div>
</div>

<section class="comments">
  <div class="container">
    <div class="row">
      <div class="col-12 col-xs-12">
        <div class="comments__top">
          <h1 class="comments__title">Список комментариев</h1>

          <!-- Формируем ссылки для применения сортировок -->
          <div class="comments__controls">
            <a href="/comments?page=1&sortDate=<?= $sort['sortType'] === 'sortDate' && $sort['sortValue'] === 'ASC' ? "DESC" : "ASC" ?> " class="comments__controls-link comments__controls-date">
              <span>По дате:</span>   
              <i class="arrow <?= $sort['sortType'] === 'sortDate' && $sort['sortValue'] === 'ASC' ? "down" : "up" ?>"></i>
            </a>
            <a href="/comments?page=1&sortId=<?= $sort['sortType'] === 'sortId' && $sort['sortValue'] === 'ASC' ? "DESC" : "ASC" ?>" class="comments__controls-link comments__controls-id">
               <span>По ID:</span> 
              <i class="arrow <?= $sort['sortType'] === 'sortId' && $sort['sortValue'] === 'ASC' ? "down" : "up" ?>"></i>
            </a>
          </div>

        <!-- Отображение комментариев -->
        </div>
          <div class="comments__items">
            <?php if( !empty($comments) ):  ?>
              <?php foreach($comments as $key => $comment): ?>
                <div class="comments__item <?php $array = ['red', 'cyan', 'orange']; echo $array[$key] ?>">
                  <div class="comments__item-head">
                    <div class="comments__item-head-left">
                      <h2 class="comments__item-name"><?= esc($comment['name']); ?></h2>
                      <p class="comments__item-date"><?= esc($comment['date']); ?></p>
                    </div>
                    <div class="comments__item-head-right">
                      <!-- Добавляем id в дата аттрибут, чтобы было удобно работать через js -->
                      <a class="comments__item-delete close" href="#" data-id="<?= esc($comment['id']) ?>"></a>
                    </div>
                  </div>
                  <p class="comments__item-text"><?= esc($comment['text']); ?></p>
                </div>
              <?php endforeach; ?>

            <?php else: ?>
              <h3 class="comments__item-placeholder">Пока не добавлено ни одного комментария.</h3>
            <?php endif;?>
          </div>
        

      </div>
    </div>
  </div>
</section>

<!-- Логика отображения навигации -->
<nav class="navigation-panel mt-5">
  <ul class="pagination justify-content-center">
    <li class="page-arrow page-previous page-item <?= $currentPageNumber == 1 ? 'disabled' : '' ?>">
      <a class="page-link" href="/comments?page=<?= esc($currentPageNumber - 1)
        . ($sort['sortType'] && $sort['sortValue'] ? esc('&'.$sort['sortType'].'='.$sort['sortValue']) : '');
        ?>">
          Назад
      </a>
    </li>
    <!-- На элемент текущей страницы ставится дополнительный класс -->
    <?php if( $totalNumberOfPages !== 1 ):  ?>
      <?php for($i = 1; $i <= $totalNumberOfPages; $i++): ?>
        <li class="page-item <?= $i == $currentPageNumber ? 'active' : '' ?>">
          <a class="page-link" href="/comments?page=<?= esc($i)
          . ($sort['sortType'] && $sort['sortValue'] ? esc('&'.$sort['sortType'].'='.$sort['sortValue']) : '');
          ?>">
            <?= esc($i); ?>
          </a>  
        </li>
      <?php endfor; ?>
    <?php endif;?>
    <li class="page-arrow page-next page-item <?= $currentPageNumber == $totalNumberOfPages || $totalNumberOfPages == 0   ? 'disabled' : '' ?>">
      <a class="page-link" href="/comments?page=<?= esc($currentPageNumber + 1)
        . ($sort['sortType'] && $sort['sortValue'] ? esc('&'.$sort['sortType'].'='.$sort['sortValue']) : '');
        ?>">
          Вперед
      </a>
    </li>
  </ul>
</nav>

<section class="add-comment mt-5">
  <div class="container">
    <form class="add-comment__form">
      <div class="row">
        <div class="col">
          <h3 class="add-comment__title">Добавить новый комментарий</h3>
        </div>
      </div>
      <div class="row mt-2">
        <div class="col-12 col-md-6">
          <label for="emailInput">E-mail</label>
          <input class="form-control" type="email" name="name" id="emailInput" placeholder="email@domain.com">
        </div>
        <div class="col-12 col-md-6">
          <label for="dateInput">Дата</label>
          <input class="form-control picker" type="date" name="date" id="dateInput" placeholder="name@example.com">
        </div>
      </div>
      <div class="row mt-2">
        <div class="col">
          <label for="textInput">Текст комментария</label>
          <textarea class="form-control" name="text" id="textInput" rows="3"></textarea>
        </div>
      </div>
      <div class="row mt-2 ">
        <div class="col">
          <button class="add-comment__submit-btn" type="submit">Добавить</button>
        </div>
      </div>
    </form>
  </div>
</section>

<script src="/js/main.js"></script>

</body>
</html>
