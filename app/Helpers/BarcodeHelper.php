<?php

namespace App\Helpers;

class BarcodeHelper
{
    protected static array $patterns = [
        '212222','222122','222221','121223','121322','131222','122213','122312','132212','221213',
        '221312','231212','112232','122132','122231','113222','123122','123221','223211','221132',
        '221231','213212','223112','312131','311222','321122','321221','312212','322112','322211',
        '212123','212321','232121','111323','131123','131321','112313','132113','132311','211313',
        '231113','231311','112133','112331','132131','113123','113321','133121','313121','211331',
        '231131','213113','213311','213131','311123','311321','331121','312113','312311','332111',
        '314111','221411','431111','111224','111422','121124','121421','141122','141221','112214',
        '112412','122114','122411','142112','142211','241211','221114','413111','241112','134111',
        '111242','121142','121241','114212','124112','124211','411212','421112','421211','212141',
        '214121','412121','111143','111341','131141','114113','114311','411113','411311','113141',
        '114131','311141','411131','211412','211214','211232',
    ];

    public static function code128B(string $data): string
    {
        $len = strlen($data);
        if ($len === 0) {
            return '';
        }

        $modules = [];
        $checksum = 104;
        $weight = 1;

        $modules = array_merge($modules, self::expand(self::$patterns[104]));

        for ($i = 0; $i < $len; $i++) {
            $val = ord($data[$i]) - 32;
            if ($val < 0 || $val > 95) {
                $val = 95;
            }
            $modules = array_merge($modules, self::expand(self::$patterns[$val]));
            $checksum += $val * $weight;
            $weight++;
        }

        $check = $checksum % 103;
        $modules = array_merge($modules, self::expand(self::$patterns[$check]));
        $modules = array_merge($modules, [2, 3, 3, 1, 1, 1, 2]);

        return self::render($modules);
    }

    protected static function expand(string $pattern): array
    {
        return array_map('intval', str_split($pattern));
    }

    protected static function render(array $modules): string
    {
        $html = '<table cellpadding="0" cellspacing="0" style="border:none;border-collapse:collapse;"><tr style="line-height:0;">';
        $black = true;
        foreach ($modules as $w) {
            $bg = $black ? '#000' : '#fff';
            $html .= '<td style="width:' . $w . 'px;height:50px;background:' . $bg . ';padding:0;margin:0;border:none;font-size:0;"></td>';
            $black = !$black;
        }
        $html .= '</tr></table>';
        return $html;
    }
}
