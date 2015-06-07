<?php

/**
 * @category  Sing
 * @author    t.watanabe
 * @since     2015/03/28
 */
class Sing_Security
{
    /**
     * ランダムなユニーク文字列を生成し返します。
     *
     * @return string 40桁の文字列
     */
    public static function uniqString()
    {
        return sha1(uniqid(mt_rand(), true));
    }

    /**
     * 平文のパスワード値からセキュア（ソルト・アンド・ペッパー方式）なパスワードを生成し返します。
     *
     * @param string $normalPass
     * @param string $prefix
     * @param string $suffix
     * @return string 40桁のパスワード
     */
    public static function securePassword($normalPass, $prefix = 'u83cLs0Q', $suffix = '00aSE4li')
    {
        return sha1($prefix. md5($normalPass). $suffix);
    }

    /**
     * 桁数を指定してランダムでほぼユニークな文字列を生成し返します。
     *
     * @param int $len 生成する文字列の桁数
     * @return string
     */
    public static function uniqRandomString($len)
    {
        $rand = substr(sha1(microtime()), 0, $len);
        $workRand = '';
        foreach (str_split($rand) as $value) {
            if ((ctype_alpha($value)) && (rand()%2 == 0)) {
                $workRand .= strtoupper($value);
            } else {
                $workRand .= $value;
            }
        }
        return $workRand;
    }

    /* ------------------------------------------------------------------------------------------------------------- */

    /**
     * 文字列をAES128bit/ECBで暗号化して返します。
     *
     * @param string $key              - 暗号化共通鍵（128bit = 16byte） 半角英数字16桁
     * @param string $target           - プレーンテキスト
     * @return mixed 暗号化されたデータを文字列で返します、失敗した場合に FALSE を返します
     */
    public static function encryptAes($key, $target)
    {
        srand();
        $size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($size, MCRYPT_RAND);

        // PKCS#5でパディング
        $block_size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
        $target = self::pkcs5_pad($target, $block_size);

        // 暗号化
        return mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $target, MCRYPT_MODE_ECB, $iv);
    }

    /**
     * AES128bit/ECBで暗号化された文字列を複合して返します。
     *
     * @param string $key              - 暗号化共通鍵（128bit = 16byte） 半角英数字16桁
     * @param string $target           - 暗号化済テキスト
     * @return mixed 復号化されたデータを文字列で返します、失敗した場合に FALSE を返します
     */
    public static function decryptAes($key, $target)
    {
        srand();
        $size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($size, MCRYPT_RAND);

        // 複合
        $data = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $target, MCRYPT_MODE_ECB, $iv);

        // PKCS#5のパディングを除去
        return self::pkcs5_unpad($data);
    }

    /**
     * パス指定したファイルをAES128bit/ECBで暗号化します。
     *
     * @param string $key              - 暗号化共通鍵（128bit = 16byte） 半角英数字16桁
     * @param string $filepath         - ファイルパス
     * @return boolean
     */
    public static function encryptAesFile($key, $filepath)
    {
        // ファイルを読込＆バイナリモードで開く
        $fhr = fopen($filepath,"rb");
        if (!$fhr) {
            return FALSE;
        }

        // ファイルの内容を読み込む
        if (filesize($filepath) !==0 ) {
            $rowData = fread($fhr, filesize($filepath));
            // ファイルを閉じる
            fclose($fhr);
        } else {
            return FALSE;
        }

        // バイナリ暗号化
        $cryptData = self::encryptAes($key, $rowData);

        if (!$cryptData) {
            return FALSE;
        }

        // ファイルを書出＆バイナリモードで開く
        $fhw = fopen($filepath,"wb");
        if (!$fhw) {
            return FALSE;
        }

        // ファイルの内容を上書きする
        fwrite($fhw, $cryptData);

        // ファイルを閉じる
        fclose($fhw);

        return true;
    }

    /**
     * パス指定したファイルをAES128bit/ECBで複合化します。
     *
     * @param string $key              - 暗号化共通鍵（128bit = 16byte） 半角英数字16桁
     * @param string $filepath         - ファイルパス
     * @return boolean
     */
    public static function decryptAesFile($key, $filepath)
    {
        // ファイルを読込＆バイナリモードで開く
        $fhr = fopen($filepath,"rb");
        if (!$fhr) {
            return false;
        }

        // ファイルの内容を読み込む
        if (filesize($filepath) !==0 ) {
            $rowData = fread($fhr, filesize($filepath));
            // ファイルを閉じる
            fclose($fhr);
        } else {
            return false;
        }

        // バイナリ暗号化
        $cryptData = self::decryptAes($key, $rowData);

        if (!$cryptData) {
            return false;
        }

        // ファイルを書出＆バイナリモードで開く
        $fhw = fopen($filepath,"wb");
        if (!$fhw) {
            return false;
        }

        // ファイルの内容を上書きする
        fwrite($fhw, $cryptData);

        // ファイルを閉じる
        fclose($fhw);

        return true;
    }

    /**
     * PKCS#5でパディングします。
     *
     * @param unknown $data
     * @param unknown $blocksize
     * @return mixed
     */
    private static function pkcs5_pad($data, $blocksize)
    {
        $pad = $blocksize - (strlen($data) % $blocksize);
        return $data . str_repeat(chr($pad), $pad);
    }

    /**
     * PKCS#5のパディングを除去します。
     *
     * @param unknown $data
     * @return mixed
     */
    private static function pkcs5_unpad($data)
    {
        return substr($data, 0, ord(substr($data, -1, 1)) * -1);
    }
}