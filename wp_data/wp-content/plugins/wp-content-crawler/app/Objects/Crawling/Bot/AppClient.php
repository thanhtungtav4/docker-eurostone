<?php
/**
 * Created by PhpStorm.
 * User: tsaricam
 * Date: 24/03/2022
 * Time: 21:47
 *
 * @since 1.12.0
 */

namespace WPCCrawler\Objects\Crawling\Bot;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\BrowserKit\Response;
use function in_array;
use function is_array;

/**
 * This class is used to make HTTP requests.
 *
 * This is extracted from Goutte, as its new versions does not allow using Guzzle as a client. We want to use Guzzle.
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Michael Dowling <michael@guzzlephp.org>
 * @author Charles Sarrazin <charles@sarraz.in>
 * @author Kévin Dunglas <dunglas@gmail.com>
 * @since 1.12.0
 */
class AppClient extends AbstractBrowser {

    /** @var Client|null */
    protected $client;

    /** @var array<string, string> */
    private $headers = [];

    /** @var array|null */
    private $auth;

    public function setClient(Client $client): self {
        $this->client = $client;
        return $this;
    }

    public function getClient(): Client {
        if (!$this->client) {
            $this->client = new Client([
                RequestOptions::ALLOW_REDIRECTS => false,
                RequestOptions::COOKIES         => true,
            ]);
        }

        return $this->client;
    }

    public function setHeader(string $name, string $value): self {
        $this->headers[strtolower($name)] = $value;
        return $this;
    }

    public function removeHeader(string $name): void {
        unset($this->headers[strtolower($name)]);
    }

    public function resetHeaders(): self {
        $this->headers = [];
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function restart(): void {
        parent::restart();
        $this->resetAuth()
            ->resetHeaders();
    }

    public function setAuth(string $user, string $password = '', string $type = 'basic'): self {
        $this->auth = [$user, $password, $type];
        return $this;
    }

    public function resetAuth(): self {
        $this->auth = null;
        return $this;
    }

    /**
     * @param Request $request
     * @return Response
     * @throws GuzzleException
     */
    protected function doRequest($request): Response {
        $headers = [];
        foreach ($request->getServer() as $key => $val) {
            $key = strtolower(str_replace('_', '-', $key));
            $contentHeaders = ['content-length' => true, 'content-md5' => true, 'content-type' => true];
            if (0 === strpos($key, 'http-')) {
                $headers[substr($key, 5)] = $val;
            } // CONTENT_* are not prefixed with HTTP_
            elseif (isset($contentHeaders[$key])) {
                $headers[$key] = $val;
            }
        }

        $domain = parse_url($request->getUri(), PHP_URL_HOST);
        $cookies = is_string($domain)
            ? CookieJar::fromArray($this->getCookieJar()->allRawValues($request->getUri()), $domain)
            : new CookieJar();

        $requestOptions = [
            RequestOptions::COOKIES         => $cookies,
            RequestOptions::ALLOW_REDIRECTS => false,
            RequestOptions::AUTH            => $this->auth,
        ];

        if (!in_array($request->getMethod(), ['GET', 'HEAD'])) {
            if (null !== $content = $request->getContent()) {
                $requestOptions[RequestOptions::BODY] = $content;
            } else {
                if ($files = $request->getFiles()) {
                    $requestOptions[RequestOptions::MULTIPART] = [];

                    $this->addPostFields($request->getParameters(), $requestOptions[RequestOptions::MULTIPART]);
                    $this->addPostFiles($files, $requestOptions[RequestOptions::MULTIPART]);
                } else {
                    $requestOptions[RequestOptions::FORM_PARAMS] = $request->getParameters();
                }
            }
        }

        if (!empty($headers)) {
            $requestOptions[RequestOptions::HEADERS] = $headers;
        }

        $method = $request->getMethod();
        $uri = $request->getUri();

        foreach ($this->headers as $name => $value) {
            $requestOptions[RequestOptions::HEADERS][$name] = $value;
        }

        // Let BrowserKit handle redirects
        try {
            $response = $this->getClient()->request($method, $uri, $requestOptions);
        } catch (RequestException $e) {
            $response = $e->getResponse();
            if (null === $response) {
                throw $e;
            }
        }

        return $this->createResponse($response);
    }

    protected function addPostFiles(array $files, array &$multipart, string $arrayName = ''): void {
        if (empty($files)) {
            return;
        }

        foreach ($files as $name => $info) {
            if (!empty($arrayName)) {
                $name = $arrayName . '[' . $name . ']';
            }

            $file = [
                'name' => $name,
            ];

            if (is_array($info)) {
                if (isset($info['tmp_name'])) {
                    if ('' !== $info['tmp_name']) {
                        $file['contents'] = fopen($info['tmp_name'], 'r');
                        if (isset($info['name'])) {
                            $file['filename'] = $info['name'];
                        }
                    } else {
                        continue;
                    }
                } else {
                    $this->addPostFiles($info, $multipart, $name);
                    continue;
                }
            } else {
                $file['contents'] = fopen($info, 'r');
            }

            $multipart[] = $file;
        }
    }

    public function addPostFields(array $formParams, array &$multipart, string $arrayName = ''): void {
        foreach ($formParams as $name => $value) {
            if (!empty($arrayName)) {
                $name = $arrayName . '[' . $name . ']';
            }

            if (is_array($value)) {
                $this->addPostFields($value, $multipart, $name);
            } else {
                $multipart[] = [
                    'name'     => $name,
                    'contents' => $value,
                ];
            }
        }
    }

    protected function createResponse(ResponseInterface $response): Response {
        return new Response((string) $response->getBody(), $response->getStatusCode(), $response->getHeaders());
    }

}