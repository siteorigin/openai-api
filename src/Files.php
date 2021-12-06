<?php

namespace SiteOrigin\OpenAI;

use GuzzleHttp\Psr7\Utils;
use SiteOrigin\OpenAI\Exception\InvalidArgumentException;

/**
 * Class Files
 * Interact with OpenAI files.
 *
 * @package SiteOrigin\OpenAI
 */
class Files extends Request
{
    const SEARCH = 'search';
    const ANSWERS = 'answers';
    const CLASSIFICATIONS = 'classifications';
    const FILE_TUNE = 'fine-tune';

    /**
     * Return a list of files.
     *
     * @return object
     * @see https://beta.openai.com/docs/api-reference/files/list
     */
    public function list(): object
    {
        return json_decode($this->request('GET', 'files')->getBody()->getContents());
    }

    /**
     * @param string $filename
     * @param string|array|resource $contents The contents of the file as a string, array of objects or file resource.
     * @param string $purpose The purpose of the file. Either "search", "answers" or "classifications".
     * @return object Creation confirmation
     * @see https://beta.openai.com/docs/api-reference/files/upload
     */
    public function create(string $filename, mixed $contents, string $purpose): object
    {
        if (is_array($contents)) {
            // Create a stream from the array that gives us a properly encoded file
            $contents = function () use ($contents) {
                foreach ($contents as $l) {
                    yield json_encode($l) . "\n";
                }
            };
            $contents = Utils::streamFor($contents());
        } elseif (is_string($contents)) {
            $contents = Utils::streamFor($contents);
        } elseif (! is_resource($contents)) {
            throw new InvalidArgumentException('Invalid file contents.');
        }

        $response = $this->request('POST', 'files', [
            'multipart' => [
                [
                    'Content-type' => 'multipart/form-data',
                    'name' => 'file',
                    'filename' => $filename,
                    'contents' => $contents,
                ],
                [
                    'name' => 'purpose',
                    'contents' => $purpose,
                ],
            ],
        ]);

        return json_decode($response->getBody()->getContents());
    }

    /**
     * Retrieve a file by its filename.
     *
     * @param string $filename The filename of the file.
     * @return object|null The file object, or null if the file couldn't be found.
     * @see https://beta.openai.com/docs/api-reference/files/retrieve
     */
    public function retrieveByFilename(string $filename): ?object
    {
        $files = $this->list();
        foreach ($files->data as $file) {
            if ($file->filename == $filename) {
                return $file;
            }
        }

        return null;
    }

    /**
     * Retrieve a file.
     *
     * @param string $id The ID of the file.
     * @return mixed|null The file object, or null if the file couldn't be found.
     * @see https://beta.openai.com/docs/api-reference/files/retrieve
     */
    public function retrieve(string $id): ?object
    {
        $response = $this->request('GET', sprintf('files/%s', $id));

        return json_decode($response->getBody()->getContents());
    }

    /**
     * @param string $filename The files filename.
     * @return object|null A delete confirmation object.
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @see https://beta.openai.com/docs/api-reference/files/delete
     */
    public function deleteByFilename(string $filename): ?object
    {
        $files = $this->list();
        foreach ($files->data as $file) {
            if ($file->filename == $filename) {
                return $this->delete($file->id);
            }
        }

        return null;
    }

    /**
     * Delete a file by its ID.
     *
     * @param string $id The file ID.
     * @return object|null A delete confirmation object.
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @see https://beta.openai.com/docs/api-reference/files/delete
     */
    public function delete(string $id): ?object
    {
        $response = $this->request('DELETE', sprintf('files/%s', $id));

        return json_decode($response->getBody()->getContents());
    }

    /**
     * Retrieve the content of the file.
     *
     * @param string $id
     * @return string
     */
    public function retrieveContent(string $id): string
    {
        $response = $this->request('GET', sprintf('files/%s/content', $id));
        return $response->getBody()->getContents();
    }
}
