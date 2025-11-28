<?php
// caca_loop_v2.php - Rastreador Agressivo
// Ignora espaços e quebras de linha para achar o problema

echo "--- INICIANDO RASTREIO AGRESSIVO ---\n";

$dir = __DIR__ . '/module';
if (!is_dir($dir)) die("Pasta module não encontrada!");

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
$regexIterator = new RegexIterator($iterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

$alvos = [
    // Removemos espaços da busca para garantir que pegue tudo
    "translator" => "/'translator'\s*=>\s*['\"]MvcTranslator['\"]/i",
    "validator"  => "/'ValidatorManager'\s*=>\s*['\"]Laminas\\\\Validator\\\\ValidatorPluginManager['\"]/i",
    "adapter"    => "/'Laminas\\\\Db\\\\Adapter\\\\Adapter'\s*=>\s*['\"]Laminas\\\\Db\\\\Adapter\\\\Adapter['\"]/i"
];

$encontrados = 0;

foreach ($regexIterator as $file) {
    $path = $file[0];
    
    // Ignora pastas de cache/vendor
    if (strpos($path, 'vendor') !== false || strpos($path, 'cache') !== false) continue;

    $content = file_get_contents($path);
    // Remove espaços em branco e quebras de linha para a busca ficar infalível
    $cleanContent = preg_replace('/\s+/', '', $content); 
    
    // Simplifica a busca para strings exatas (devido a remoção de espaços acima)
    if (strpos($cleanContent, "'translator'=>'MvcTranslator'") !== false) {
        echo "\n[ALVO LOCALIZADO - TRANSLATOR]\nARQUIVO: $path\n";
        $encontrados++;
    }
    if (strpos($cleanContent, "'ValidatorManager'=>'Laminas\Validator\ValidatorPluginManager'") !== false) {
        echo "\n[ALVO LOCALIZADO - VALIDATOR]\nARQUIVO: $path\n";
        $encontrados++;
    }
}

if ($encontrados == 0) {
    echo "\n--- NADA ENCONTRADO NOS MÓDULOS ---\n";
    echo "Verifique 'config/autoload/global.php' manualmente.\n";
} else {
    echo "\n--- FIM. CORRIJA OS ARQUIVOS LISTADOS ACIMA ---\n";
}