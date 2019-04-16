<?php

namespace AmoCrm\Errors;

/**
 * Class PicErrors.
 *
 * @author Vitaly Dergunov <correcter@inbox.ru>
 */
class PicErrors
{
    const ERR_EMPTY_DATA = 'Тело запроса пустое или не в формате JSON';
    const ERR_EMPTY_CONTENT = 'Не задан обязательный элемент - content';
    const ERR_EMPTY_SOURCE = 'Не задан обязательный элемент - source';
    const ERR_EMPTY_SECRET = 'Не задан обязательный элемент - secret';
    const ERR_UNKNOWN_SOURCE = 'Неизвестный источник данных';
    const ERR_MISMATCH_SECRET = 'Открытый ключ не совпадает';
    const ERR_EMPTY_ID = 'Не задан обязательный элемент - ID данных lead формы';
    const ERR_EMPTY_CONTENT_FORM_NAME = 'Не задан обязательный элемент form_name';
    const ERR_EMPTY_CONTENT_FULL_NAME = 'Не задан обязательный элемент - full_name';
    const ERR_EMPTY_CONTENT_EMAIL = 'Не задан обязательный элемент - email';
    const ERR_EMPTY_CONTENT_PHONE = 'Не задан обязательный элемент - phone';
    const ERR_LEAD_ALREADY_RECEIVED = 'Данные lead формы с указанным ID уже были получены ранее';
}
