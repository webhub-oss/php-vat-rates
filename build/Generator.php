<?php

namespace Webhub\Vat\Build;

require __DIR__.'/../vendor/autoload.php';

use GuzzleHttp\Client;
use League\Csv\Reader;
use Zend\Code\Generator\ValueGenerator;

class Generator
{

    /**
     * @var string URL to CSV source
     */
    protected $source = 'https://raw.githubusercontent.com/kdeldycke/vat-rates/master/vat_rates.csv';

    /**
     * @var Client
     */
    protected $client;

    /**
     * @throws \Exception
     */
    public static function generate()
    {
        (new static)->build();
    }

    public function __construct(string $source = null)
    {
        if ($source !== null) {
            $this->source = $source;
        }

        $this->client = new Client;
    }

    /**
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function build()
    {
        $response = $this->client->request('GET', $this->source);

        $reader = $this->makeReader($response->getBody()->detach());

        $rates = iterator_to_array($reader->getRecords());

        $export = $this->toPhp($rates);

        file_put_contents(__DIR__.'/../src/data.php', $export);
    }

    /**
     * @param resource $resource
     * @return Reader
     * @throws \League\Csv\Exception
     */
    protected function makeReader($resource) : Reader
    {
        $reader = Reader::createFromStream($resource);

        return $reader->setDelimiter(',')
            ->setHeaderOffset(0);
    }

    /**
     * @param array $data
     * @return string
     */
    protected function toPhp(array $data) : string
    {
        $generator = new ValueGenerator($data, ValueGenerator::TYPE_ARRAY_SHORT);
        $generator->setIndentation('  '); // 2 spaces
        $data = $generator->generate();

        return "<?php return $data;";
    }
}
