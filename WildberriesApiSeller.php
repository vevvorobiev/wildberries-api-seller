<?php

/**
 * Wildberries REST API Client
 *
 * @see Wildberries REST API Documentation
 *      (https://suppliers-api.wildberries.ru/swagger/doc.json)

 *
 * @author JackVorobey77 vevvorobiev@gmail.com


 * Version 1.0

 */
class WildberriesApiSeller
 */
class WbApiClient
{
    /**
     * Константы уровня вывода отладочной информации
     *
     * @var int
     */
    public const DEBUG_NONE = 0;    // 0 - не выводить
    public const DEBUG_URL = 1;     // 1 - URL запросов/ответов
    public const DEBUG_HEADERS = 2; // 2 - заголовки запросов/ответов
    public const DEBUG_CONTENT = 3; // 3 - содержимое запросов/ответов

    /**
     * Уровень вывода отладочной информации по-умолчанию
     *
     * @var int
     */
    public $debugLevel = self::DEBUG_NONE;

    /**
     * Максимальное число HTTP запросов в секунду (0 - троттлинг отключен)
     *
     * @var float
     */
    public $throttle = 3;

    /**
     * Коды статуса НТТР, соответствующие успешному выполнению запроса
     *
     * @var array
     */
    public $successStatusCodes = [ 200 ];

    /**
     * Таймаут соединения для cUrl, секунд
     *
     * @var int
     */
    public $curlConnectTimeout = 30;

    /**
     * Таймаут обмена данными для cUrl, секунд
     *
     * @var int
     */
    public $curlTimeout = 300;

    /**
     * Время последнего запроса, микросекунды
     *
     * @var float
     */
    private $lastRequestTime = 0;

    /**
     * Счетчик запросов для отладочных сообщений
     *
     * @var int
     */
    private $requestCounter = 0;

    /**
     * Ресурс cURL
     *
     * @var \CurlHandle
     */
    private $curl;

    /**
     * Адрес API сервиса
     *
     * @var string
     */
private $apiUrl = 'https://suppliers-api.wildberries.ru';
/**
     * Токен партнера
     *
     * @var string
     */
    private $token;

    /**
     * Дата и время в формате стандарта RFC3339
     *
     * @var string
     */
    private $dateFrom;

    /**
     * Wildberries API конструктор
     *
     * @param $token
     *
     */
    public function __construct( ?string $token = '')

    {
        if ( empty( $token ) ) {
echo 'The Token is not specified';

        }

        $this->token = $token;
    }

    /**
     * Получить токен
     *
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }
/**
* Генерация случайного uuid v4
*/
public function guidv4($data)
{ 

assert(strlen($data) == 16); 
$data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100 
$data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10 
return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4)); 
}
function fileUpload( $filename )
{ 
$type=mime_content_type($filename);
$data=array('data' => '@' .realpath($filename.';type='.$type);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://suppliers-api.wildberries.ru/card/upload/file/multipart');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data );

$headers = array();
$headers[] = 'Accept: */*';
$headers[] = 'X-File-Id: '.guidv4(openssl_random_pseudo_bytes(16));
$headers[] = 'Authorization: '.$this->token;
$headers[] = 'Content-Type: multipart/form-data';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$result = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);
return $result;

}
    /**
     * Формируем и отправляем запрос к API сервису
     *
     * @param string $path
     * @param string $method
     * @param array  $data
     *
     */

    
protected function sendRequest( string $path, string $method = 'GET', $data)


    {
        if ( empty( $this->token ) ) {
            return $this->handleError( 'The Token is not specified' );
        }
        $url = $this->apiUrl . '/' . $path;
        $method = strtoupper( $method );

        $this->curl = curl_init();

        switch ( $method ) {
            case 'POST':
curl_setopt( $this->curl, CURLOPT_POST, 1 );
curl_setopt( $this->curl, CURLOPT_POSTFIELDS, $data );
$headers=array();
$headers[] = 'Accept: application/json';
$headers[] = 'Authorization: '. $this->token; 
$headers[] = 'Content-Type: application/json';

                break;
            case 'PUT':
            curl_setopt( $this->curl, CURLOPT_CUSTOMREQUEST, 'PUT' );
            curl_setopt( $this->curl, CURLOPT_POSTFIELDS, $data );
$headers=array();
$headers[] = 'Accept: application/json';
$headers[] = 'Authorization: '. $this->token; 
$headers[] = 'Content-Type: application/json';
                break;
            case 'DELETE':
            curl_setopt( $this->curl, CURLOPT_CUSTOMREQUEST, 'DELETE' );
            curl_setopt( $this->curl, CURLOPT_POSTFIELDS, $data );
$headers=array();
$headers[] = 'Accept: application/json';
$headers[] = 'Authorization: '. $this->token; 
$headers[] = 'Content-Type: application/json';
                break;
            default:
            curl_setopt( $this->curl, CURLOPT_CUSTOMREQUEST, 'GET');
$headers = array();
$headers[] = 'Accept: application/json';
$headers[] = 'Authorization: '.$this->token;

                if ( !empty( $data ) ) {
                    $url .= '?' . http_build_query( $data );
                }
        }

        curl_setopt( $this->curl, CURLOPT_URL, $url );
        curl_setopt( $this->curl, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $this->curl, CURLOPT_SSL_VERIFYHOST, false );
        curl_setopt( $this->curl, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $this->curl, CURLOPT_HEADER, true );
        curl_setopt( $this->curl, CURLOPT_CONNECTTIMEOUT, $this->curlConnectTimeout );
        curl_setopt( $this->curl, CURLOPT_TIMEOUT, $this->curlTimeout );
        if ( !empty( $headers ) ) {
            curl_setopt( $this->curl, CURLOPT_HTTPHEADER, $headers );
           
        }

        $response = $this->throttleCurl();
        $deltaTime = sprintf( '%0.4f', microtime( true ) - $this->lastRequestTime );

        $curlErrors = curl_error( $this->curl );
        $curlInfo = curl_getinfo( $this->curl );
        $ipAddress = $curlInfo['primary_ip'];
        $header_size = $curlInfo['header_size'];
        $headerCode = $curlInfo['http_code'];
        $responseHeaders = trim( substr( $response, 0, $header_size ) );
        $responseBodyRaw = substr( $response, $header_size );
        $responseBody = json_decode( $responseBodyRaw );
        if ( json_last_error() !== JSON_ERROR_NONE ) {
            $responseBody = $responseBodyRaw;
        }
        unset( $response, $responseBodyRaw );
        curl_close( $this->curl );
    }

    /**
     * Обеспечивает троттлинг HTTP запросов
     *
     * @return string|false
     */
    private function throttleCurl()
    {
        do {
            if ( empty( $this->throttle ) ) {
                break;
            }

            // Вычисляем необходимое время задержки перед отправкой запроса, микросекунды
            $usleep = (int)( 1E6 * ( $this->lastRequestTime + 1 / $this->throttle - microtime( true ) ) );
            if ( $usleep <= 0 ) {
                break;
            }

            $sleep = sprintf( '%0.4f', $usleep / 1E6 );
            usleep( $usleep );
        } while ( false );

        do {
            $this->lastRequestTime = microtime( true );
            $response = curl_exec( $this->curl );

            $oneMoreTry = curl_getinfo( $this->curl, CURLINFO_RESPONSE_CODE ) == 429;
            if ( $oneMoreTry ) {
                usleep( 500000 );
            }
        } while ( $oneMoreTry );

        return $response;
    }

    /**
     * Выводит в STDOUT отладочные сообщения на заданном уровне вывода отладочной информации
     *
     * @param string
     * @param int
     *
     * @return void
     */
    protected function debug( string $message, int $callerLogLevel = 999 ): void
    {
        if ( $this->debugLevel >= $callerLogLevel ) {
            echo $message . PHP_EOL;
        }
    }

/**
* Реализация всех методов для работы с api wildberries
*/
/**

* Загрузка цен. За раз можно загрузить не более 1000 номенклатур.


*/

public function post_public_api_v1_prices ($data)
{
$requestResult = $this->sendRequest( 'public/api/v1/prices', 'POST', $data );
return $this->$requestResult;
}

/**

* Получение информации по номенклатурам, их ценам, скидкам и промокодам. Если не указывать фильтры, вернётся весь товар.


*/

public function get_public_api_v1_info ($data)
{
$requestResult = $this->sendRequest( 'public/api/v1/info', 'GET', $data );
return $this->$requestResult;
}

/**

* Установка скидок для номенклатур. Максимальное количество номенклатур на запрос - 1000


*/

public function post_public_api_v1_updateDiscounts ($data)
{
$requestResult = $this->sendRequest( 'public/api/v1/updateDiscounts', 'POST', $data );
return $this->$requestResult;
}

/**

* Сброс скидок для номенклатур


*/

public function post_public_api_v1_revokeDiscounts ($data)
{
$requestResult = $this->sendRequest( 'public/api/v1/revokeDiscounts', 'POST', $data );
return $this->$requestResult;
}

/**

* Установка промокодов для номенклатур. Максимальное количество номенклатур на запрос - 1000


*/

public function post_public_api_v1_updatePromocodes ($data)
{
$requestResult = $this->sendRequest( 'public/api/v1/updatePromocodes', 'POST', $data );
return $this->$requestResult;
}

/**

* Сброс промокодов для номенклатур


*/

public function post_public_api_v1_revokePromocodes ($data)
{
$requestResult = $this->sendRequest( 'public/api/v1/revokePromocodes', 'POST', $data );
return $this->$requestResult;
}

/**

* Возвращает список поставок


*/

public function get_api_v2_supplies ($data)
{
$requestResult = $this->sendRequest( 'api/v2/supplies', 'GET', $data );
return $this->$requestResult;
}

/**

* Добавляет к поставке заказы


*/

public function put_api_v2_supplies_id ($id,$data)

{
$requestResult = $this->sendRequest( 'api/v2/supplies/'.$id, 'PUT', $data );

return $this->$requestResult;
}

/**

* Закрывает поставку


*/

public function post_api_v2_supplies_id_close ($id,$data)

{
$requestResult = $this->sendRequest( 'api/v2/supplies/'.$id.'/close', 'POST', $data );
return $this->$requestResult;
}

/**

* Возвращает штрихкод поставки в заданном формате


*/

public function get_api_v2_supplies_id_barcode ($id,$data)

{
$requestResult = $this->sendRequest( 'api/v2/supplies/'.$id.'/barcode', 'GET', $data );
return $this->$requestResult;
}

/**

* Возвращает список заказов, закреплённых за поставкой


*/

public function get_api_v2_supplies_id_orders ($id,$data)

{
$requestResult = $this->sendRequest( 'api/v2/supplies/'.$id.'/orders', 'GET', $data );
return $this->$requestResult;
}

/**

* Возвращает список товаров поставщика с их остатками


*/

public function get_api_v2_stocks ($data)
{
$requestResult = $this->sendRequest( 'api/v2/stocks', 'GET', $data );
return $this->$requestResult;
}

/**

* Возвращает список складов поставщика


*/

public function get_api_v2_warehouses ($data)
{
$requestResult = $this->sendRequest( 'api/v2/warehouses', 'GET', $data );
return $this->$requestResult;
}

/**

* Возвращает список сборочных заданий поставщика.


*/

public function get_api_v2_orders ($data)
{
$requestResult = $this->sendRequest( 'api/v2/orders', 'GET', $data );
return $this->$requestResult;
}

/**

* Возвращает список стикеров по переданному массиву сборочных заданий.


*/

public function post_api_v2_orders_stickers ($data)
{
$requestResult = $this->sendRequest( 'api/v2/orders/stickers', 'POST', $data );
return $this->$requestResult;
}

/**

* Возвращает список стикеров в формате pdf по переданному массиву сборочных заданий.


*/

public function post_api_v2_orders_stickers_pdf ($data)
{
$requestResult = $this->sendRequest( 'api/v2/orders/stickers/pdf', 'POST', $data );
return $this->$requestResult;
}

/**

* Создание группы карточек


*/

public function post_card_batchCreate ($data)
{
$requestResult = $this->sendRequest( 'card/batchCreate', 'POST', $data );
return $this->$requestResult;
}

/**

* Получение карточки поставщика по imt id


*/

public function post_card_cardByImtID ($data)
{
$requestResult = $this->sendRequest( 'card/cardByImtID', 'POST', $data );
return $this->$requestResult;
}

/**

* Создание одной карточки


*/

public function post_card_create ($data)
{
$requestResult = $this->sendRequest( 'card/create', 'POST', $data );
return $this->$requestResult;
}

/**

* Удалить номенклатуру из карточки


*/

public function post_card_deleteNomenclature ($data)
{
$requestResult = $this->sendRequest( 'card/deleteNomenclature', 'POST', $data );
return $this->$requestResult;
}

/**

* Выгрузить файл из хранилища


*/

public function get_card_file_supplierID_fileID ($supplierID,$fileID,$data)

{
$requestResult = $this->sendRequest( 'card/file/'.$supplierID.'/'.$fileID, 'GET', $data );

return $this->$requestResult;
}

/**

* Сгенерировать шк


*/

public function post_card_getBarcodes ($data)
{
$requestResult = $this->sendRequest( 'card/getBarcodes', 'POST', $data );
return $this->$requestResult;
}

/**

* Получить список карточек поставщика с фильтром и сортировкой


*/

public function post_card_list ($data)
{
$requestResult = $this->sendRequest( 'card/list', 'POST', $data );
return $this->$requestResult;
}

/**

* Обновить карточку


*/

public function post_card_update ($data)
{
$requestResult = $this->sendRequest( 'card/update', 'POST', $data );
return $this->$requestResult;
}

/**

* Загрузить файл в хранилище ( перемещено в функцию uploadFile )



*/


/**

* Получение конфигурации предмета


*/

public function get_api_v1_config_get_object_translated ($data)
{
$requestResult = $this->sendRequest( 'api/v1/config/get/object/translated', 'GET', $data );
return $this->$requestResult;
}

/**

* Поиск предмета по паттерну


*/

public function get_api_v1_config_get_object_list ($data)
{
$requestResult = $this->sendRequest( 'api/v1/config/get/object/list', 'GET', $data );
return $this->$requestResult;
}

/**

* Справочник основных и дополнительных цветов


*/

public function get_api_v1_directory_colors ($data)
{
$requestResult = $this->sendRequest( 'api/v1/directory/colors', 'GET', $data );
return $this->$requestResult;
}

/**

* Справочник пол


*/

public function get_api_v1_directory_kinds ($data)
{
$requestResult = $this->sendRequest( 'api/v1/directory/kinds', 'GET', $data );
return $this->$requestResult;
}

/**

* Справочник стран


*/

public function get_api_v1_directory_countries ($data)
{
$requestResult = $this->sendRequest( 'api/v1/directory/countries', 'GET', $data );
return $this->$requestResult;
}

/**

* Справочник коллекций


*/

public function get_api_v1_directory_collections ($data)
{
$requestResult = $this->sendRequest( 'api/v1/directory/collections', 'GET', $data );
return $this->$requestResult;
}

/**

* Справочник сезонов


*/

public function get_api_v1_directory_seasons ($data)
{
$requestResult = $this->sendRequest( 'api/v1/directory/seasons', 'GET', $data );
return $this->$requestResult;
}

/**

* Справочник комплектации


*/

public function get_api_v1_directory_contents ($data)
{
$requestResult = $this->sendRequest( 'api/v1/directory/contents', 'GET', $data );
return $this->$requestResult;
}

/**

* Справочник составов


*/

public function get_api_v1_directory_consists ($data)
{
$requestResult = $this->sendRequest( 'api/v1/directory/consists', 'GET', $data );
return $this->$requestResult;
}

/**

* Справочник тнвэд


*/

public function get_api_v1_directory_tnved ($data)
{
$requestResult = $this->sendRequest( 'api/v1/directory/tnved', 'GET', $data );
return $this->$requestResult;
}

/**

* Справочник дополнительных свойств


*/

public function get_api_v1_directory_options ($data)
{
$requestResult = $this->sendRequest( 'api/v1/directory/options', 'GET', $data );
return $this->$requestResult;
}

/**

* Справочник брендов


*/

public function get_api_v1_directory_brands ($data)
{
$requestResult = $this->sendRequest( 'api/v1/directory/brands', 'GET', $data );
return $this->$requestResult;
}

/**

* Справочник систем измерений


*/

public function get_api_v1_directory_si ($data)
{
$requestResult = $this->sendRequest( 'api/v1/directory/si', 'GET', $data );
return $this->$requestResult;
}

/**

* Справочник значений для дополнительных свойств


*/

public function get_api_v1_directory_ext ($data)
{
$requestResult = $this->sendRequest( 'api/v1/directory/ext', 'GET', $data );
return $this->$requestResult;
}

/**

* Справочник российских размеров


*/

public function get_api_v1_directory_wbsizes ($data)
{
$requestResult = $this->sendRequest( 'api/v1/directory/wbsizes', 'GET', $data );
return $this->$requestResult;
}

/**

* Все доступные справочники


*/

public function get_api_v1_directory_get_list ($data)
{
$requestResult = $this->sendRequest( 'api/v1/directory/get/list', 'GET', $data );
return $this->$requestResult;
}

/**

* Все доступные предметы


*/

public function get_api_v1_config_get_object_all ($data)
{
$requestResult = $this->sendRequest( 'api/v1/config/get/object/all', 'GET', $data );
return $this->$requestResult;
}

/**

* Все паренты


*/

public function get_api_v1_config_get_object_parent_list ($data)
{
$requestResult = $this->sendRequest( 'api/v1/config/get/object/parent/list', 'GET', $data );
return $this->$requestResult;
}

/**

* Получение списка объектов по паренту


*/

public function get_api_v1_config_object_byparent ($data)
{
$requestResult = $this->sendRequest( 'api/v1/config/object/byparent', 'GET', $data );
return $this->$requestResult;
}
}
