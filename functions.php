<?php
function extractFilmInfo($folderPath)
{
    $files = scandir($folderPath);
    $films = [];

    foreach ($files as $file) {
        if ($file != "." && $file != "..") {
            $filePath = $folderPath . $file;
            $fileContents = file_get_contents($filePath);

            $pattern = '/<td class="l"><h2>Страна<\/h2>:<\/td>\s*<td>(.*?)<\/td>/s';

            if (preg_match($pattern, $fileContents, $matches)) {
                $countryText = strip_tags($matches[1]);

                preg_match('/^(.*?)\s*\((.*?)\)$/', $countryText, $countryMatches);

                if (!empty($countryMatches)) {
                    $fullCountryName = trim($countryMatches[0]);
                } else {
                    $fullCountryName = trim($countryText);
                }
            } else {
                $fullCountryName = 'нет информации о стране';
            }

            $fileInfo = pathinfo($file);
            $fileNameParts = explode('-', $fileInfo['filename']);
            $filmName = implode('-', array_slice($fileNameParts, 1, -1));

            $filmInfo = [
                'name' => $filmName,
                'production_countries' => $fullCountryName
            ];

            $films[] = $filmInfo;
        }
    }
    return $films;
}
