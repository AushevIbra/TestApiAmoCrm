<?php

/**
 * 
 */
class AmoCrm
{
	private $subdomain = 'aushevibra';
	private $user = array(
  		'USER_LOGIN'=>'aushevibra@yandex.ru', #Ваш логин (электронная почта)
 		'USER_HASH'=>'ad9edde69c80301dd005b78688e53ec07689fe83' #Хэш для доступа к API (смотрите в профиле пользователя)
	);
	private $leads_id = array();
	function __construct()
	{
		 $this->auth();
		 $this->getLeads();
		 $this->setTask();
		 //var_dump($this->leads);
	}

	public function auth()
	{
		$link='https://'.$this->subdomain.'.amocrm.ru/private/api/auth.php?type=json';
		/* Нам необходимо инициировать запрос к серверу. Воспользуемся библиотекой cURL (поставляется в составе PHP). Вы также
		можете
		использовать и кроссплатформенную программу cURL, если вы не программируете на PHP. */
		$curl=curl_init(); #Сохраняем дескриптор сеанса cURL
		#Устанавливаем необходимые опции для сеанса cURL
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
		curl_setopt($curl,CURLOPT_URL,$link);
		curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
		curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($this->user));
		curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
		curl_setopt($curl,CURLOPT_HEADER,false);
		curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
		curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
		curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
		$out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
		$code=curl_getinfo($curl,CURLINFO_HTTP_CODE); #Получим HTTP-код ответа сервера
		curl_close($curl); #Завершаем сеанс cURL
		/* Теперь мы можем обработать ответ, полученный от сервера. Это пример. Вы можете обработать данные своим способом. */
		$code=(int)$code;
		$this->checkError($code);
		$Response=json_decode($out,true);
		$Response=$Response['response'];
		if(isset($Response['auth'])) #Флаг авторизации доступен в свойстве "auth"
		 echo 'Авторизация прошла успешно';
		else 
			echo 'Авторизация не удалась';
	}

	public function checkError($code)
	{
		$errors=array(
		  301=>'Moved permanently',
		  400=>'Bad request',
		  401=>'Unauthorized',
		  403=>'Forbidden',
		  404=>'Not found',
		  500=>'Internal server error',
		  502=>'Bad gateway',
		  503=>'Service unavailable'
		);
		try
		{
		  #Если код ответа не равен 200 или 204 - возвращаем сообщение об ошибке
		 if($code!=200 && $code!=204)
		    throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error',$code);
		}
		catch(Exception $E)
		{
		  die('Ошибка: '.$E->getMessage().PHP_EOL.'Код ошибки: '.$E->getCode());
		}
	}
	public function getLeads()
	{
		$link='https://'.$this->subdomain.'.amocrm.ru/api/v2/leads';
		/* Заметим, что в ссылке можно передавать и другие параметры, которые влияют на выходной результат (смотрите документацию
		выше).
		Следовательно, мы можем заменить ссылку, приведённую выше на одну из следующих, либо скомбинировать параметры так, как Вам
		необходимо. */
		$link='https://'.$this->subdomain.'.amocrm.ru/api/v2/leads?limit_rows=50';
		$link='https://'.$this->subdomain.'.amocrm.ru/api/v2/leads?limit_rows=50&limit_offset=2';
		/* Следующий запрос вернёт список сделок, у которых есть почта 'test@mail.com' */
		$link='https://'.$this->subdomain.'.amocrm.ru/api/v2/leads';
		/* Нам необходимо инициировать запрос к серверу. Воспользуемся библиотекой cURL (поставляется в составе PHP). Подробнее о
		работе с этой
		библиотекой Вы можете прочитать в мануале. */
		$curl=curl_init();
		/* Устанавливаем необходимые опции для сеанса cURL */
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
		curl_setopt($curl,CURLOPT_URL,$link);
		curl_setopt($curl,CURLOPT_HEADER,false);
		curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
		curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
		curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
		/* Вы также можете передать дополнительный HTTP-заголовок IF-MODIFIED-SINCE, в котором указывается дата в формате D, d M Y
		H:i:s. При
		передаче этого заголовка будут возвращены сделки, изменённые позже этой даты. */
		curl_setopt($curl,CURLOPT_HTTPHEADER,array('IF-MODIFIED-SINCE: Mon, 01 Aug 2013 07:07:23'));
		/* Выполняем запрос к серверу. */
		$out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
		$code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
		curl_close($curl);
		/* Теперь мы можем обработать ответ, полученный от сервера. Это пример. Вы можете обработать данные своим способом. */
		$code=(int)$code;
		$this->checkError($code);
		$Response=json_decode($out,true);
		$items=$Response['_embedded']['items'];
		foreach ($items as $lead) {
			if($lead['closest_task_at'] == 0){
				array_push($this->leads_id, $lead['id'] );
			
			} else { 
				echo "Нет сделок без открытых задач";
					
			}
		}
	}
	public function setTask()
	{
		$tasks['add'] = array();
		foreach($this->leads_id as $id) {
			array_push($tasks['add'], array(
						'element_id'=>$id, #ID сделки
						'element_type'=>2, #Показываем, что это - сделка, а не контакт
						'task_type'=>1, #Тип задачи - звонок
						'text'=>'Сделка без задачи',
						'complete_till'=>strtotime('31-08-2018') #Дата до которой необходимо завершить задачу.
						)
				);
		}

		$link='https://'.$this->subdomain.'.amocrm.ru/api/v2/tasks';
		/* Нам необходимо инициировать запрос к серверу. Воспользуемся библиотекой cURL (поставляется в составе PHP). Подробнее о
		работе с этой
		библиотекой Вы можете прочитать в мануале. */
		$curl=curl_init(); #Сохраняем дескриптор сеанса cURL
		#Устанавливаем необходимые опции для сеанса cURL
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
		curl_setopt($curl,CURLOPT_URL,$link);
		curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
		curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($tasks));
		curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
		curl_setopt($curl,CURLOPT_HEADER,false);
		curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
		curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
		curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
		$out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
		$code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
		/* Теперь мы можем обработать ответ, полученный от сервера. Это пример. Вы можете обработать данные своим способом. */
		$code=(int)$code;
		$this->checkError($code);
	}
}

 $test = new AmoCrm();
 ?>