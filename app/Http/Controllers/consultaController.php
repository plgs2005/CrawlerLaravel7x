<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Goutte\Client;
use PhpParser\Node\Stmt\Foreach_;
use Sunra\PhpSimple\HtmlDomParser;
use Symfony\Component\HttpClient\HttpClient;
use Symfony;

use function simplehtmldom_1_5\file_get_html;

class consultaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function index()
    {

        set_time_limit(0);
        //envia o headers para leitura do navegador e força ele a não usar o https
        $client = new \GuzzleHttp\Client([
            "headers" => [
                "user-agent" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.90 Safari/537.36",
            ],
            'verify' => false
        ]);



        //aqui realizei um loop para paginação dos resultados já sabendo o tamanho de listagem no caso 279 paginas 
        for ($i = 0; $i <= '279'; $i++) {
            $page = (!isset($_GET['page'])) ? 1 : $_GET['page'];
            echo "<b><br> Estamos na pagina: #" . $i . "</b><br><br>";
            // indico em qual URL ele realizara a busca
            $URL = 'https://www.guiamais.com.br/encontre?searchbox=true&what=&where=itatiba&page=' . $i;

            // realizo um request na pagina definida
            $html = $client->request("GET", $URL)->getBody();

            //gera o DOm para os resultado para podermos separar cada item
            $dom = HtmlDomParser::str_get_html($html);


            // este for pega o URL de cada empresa e resgata todos os dados da empresa
            foreach ($dom->find('meta[itemprop=url]') as $key => $link) {
                // link de cada empresa
                $urlEmpresa = $link->content;
                // realizo um novo request em cada empresa para pegar os dados e poder separar
                $html = $client->request("GET", $urlEmpresa)->getBody();
                //gero um DOm para esta pagina e separar os resultados
                $domEmpresa = HtmlDomParser::str_get_html($html);
                // pega a div de basics info da pagina 
                $basicsInfo = $domEmpresa->find('div.basicsInfo', 0);
                //busco o div extendinfo
                $extendedInfo = $domEmpresa->find('div.extendedInfo', 0);
                //busco o h1
                $titulo = $basicsInfo->find('h1', 0)->plaintext;
                //busco o div p.category
                $categoria = html_entity_decode(trim($basicsInfo->find('p.category', 0)->plaintext));
                //busco o advAdress
                $endereco = html_entity_decode(trim($extendedInfo->find('.advAddress', 0)->plaintext));
                //crio um array vazio pois a empresa pode ter mais de um telefone
                $telefones = [];

                //faço um for para preencher o array
                foreach ($extendedInfo->find('li.detail') as $li) {
                    //busco as informações de telefone
                    $telefones[] = html_entity_decode(trim($li->plaintext));
                }
                //retorna os dados na tela
                echo "<b>" . $key . "<br>&#8600<br>";

                echo "</b>" . $titulo . PHP_EOL . "<br>" . $categoria . PHP_EOL . "<br>" . $endereco . PHP_EOL;
                echo '<pre>';
                print_r($telefones);
                echo '</pre>';
                echo PHP_EOL . PHP_EOL . PHP_EOL;
            }
        }
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
