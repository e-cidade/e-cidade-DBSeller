<?php


namespace App\Domain\Core\Base\Http\Response;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\JsonResponse;

class DBJsonResponse extends JsonResponse
{
    /**
     * DBJsonResponse constructor.
     * @param null $data
     * @param string $message
     * @param int $status
     * @param bool $encode
     * @param array $headers
     * @param int $options
     * @param bool $encodeMessage
     */
    public function __construct(
        $data = null,
        $message = '',
        $status = 200,
        $encode = true,
        $headers = [],
        $options = 0,
        $encodeMessage = true
    ) {
        if ($encode) {
            if ($encodeMessage) {
                $message = utf8_encode($message);
            }
            $data = self::convertFromLatin1ToUTF8Recursively($data);
        }

        parent::__construct([
            'message' => $message,
            'error' => $this->statusCodeIsError($status),
            'data' => $data
        ], $status, $headers, $options);
    }

    /**
     * @param $statusCode
     * @return bool
     */
    private function statusCodeIsError($statusCode)
    {
        return $statusCode < 200 || $statusCode >= 300;
    }

    /**
     * Encode array from latin1 to utf8 recursively
     * @param $data
     * @return array|string
     */
    public static function convertFromLatin1ToUTF8Recursively($data)
    {
        if ($data instanceof Arrayable) {
            return $data = self::convertFromLatin1ToUTF8Recursively($data->toArray());
        } elseif (is_array($data)) {
            $ret = [];
            foreach ($data as $i => $d) {
                $ret[ $i ] = self::convertFromLatin1ToUTF8Recursively($d);
            }

            return $ret;
        } elseif (is_object($data)) {
            foreach ($data as $i => $d) {
                $data->$i = self::convertFromLatin1ToUTF8Recursively($d);
            }

            return $data;
        } else {
            if (!mb_detect_encoding($data, 'utf-8', true)) {
                return utf8_encode($data);
            }
        }

        return $data;
    }
}
