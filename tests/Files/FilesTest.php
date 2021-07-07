<?php

namespace SiteOrigin\OpenAI\Tests\API;

use function Amp\Promise\wait;
use SiteOrigin\OpenAI\Exception\ConflictException;
use SiteOrigin\OpenAI\Tests\BaseTestCase;

/**
 * Test file uploads and deletion. This shouldn't run as part of an automated test suite with production keys.
 *
 * @group nonAutoRun
 */
class FilesTest extends BaseTestCase
{
    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    protected function deleteTempFiles(): array
    {
        $client = $this->getClient();

        // Delete all the old temporary files.
        $filesDeleted = [];
        for ($run = 0; $run <= 10; $run++) {
            if ($run == 10) {
                throw new \Exception('Could not clear temporary files');
            }

            try {
                $files = $client->files()->list();
                foreach ($files->data as $file) {
                    if (str_starts_with($file->filename, '_temp_')) {
                        $deleted = $client->files()->delete($file->id);
                        $filesDeleted[] = $deleted->id;
                    }
                }

                // If we make it this far, we're done and can break out of the loop
                return $filesDeleted;
            } catch (ConflictException $e) {
                // Wait for the files to finish processing so we can delete them.
                sleep(5);
            } catch (\Throwable $e) {
                // Throw all other exceptions as a way to break from this
                throw $e;
            }
        }

        return $filesDeleted;
    }

    public function test_create_files()
    {
        $client = $this->getClient();

        // Test creating a file from an array of associative arrays
        $name = '_temp_' . substr(md5(rand(0, 65536)), 0, 12) . '.json';
        $data = [
            [ 'text' => 'This is the future of AI' ],
            [ 'text' => 'Buy Bitcoin' ],
        ];
        $created = $client->files()->create($name, $data, 'search');
        $this->assertEquals('uploaded', $created->status);
        $this->assertEquals($name, $created->filename);

        // Test retrieving a file by a filename (which also tests getting the file by an ID
        $f = $client->files()->retrieveByFilename($name);
        $this->assertNotEmpty($f->id);

        // Test creating a file from a file stream
        $name = '_temp_' . substr(md5(rand(0, 65536)), 0, 12) . '.json';
        $created = $client->files()->create($name, fopen(__DIR__.'/../data/search.json.txt', 'r'), 'search');
        $this->assertEquals('uploaded', $created->status);
        $this->assertEquals($name, $created->filename);

        // Test creating a file from a string
        $name = '_temp_' . substr(md5(rand(0, 65536)), 0, 12) . '.json';
        $created = $client->files()->create($name, file_get_contents(__DIR__.'/../data/search.json.txt'), 'search');
        $this->assertEquals('uploaded', $created->status);
        $this->assertEquals($name, $created->filename);

        // Testing listing the files. Should return 3 files.
        $files = $client->files()->list()->data;
        $this->assertGreaterThanOrEqual(3, count($files));

        // Delete all the temp files
        $deleted = $this->deleteTempFiles();
        $this->assertGreaterThanOrEqual(3, count($deleted));
    }

    /**
     * @throws \Throwable
     */
    protected function tearDown(): void
    {
        parent::tearDown(); // TODO: Change the autogenerated stub
    }
}