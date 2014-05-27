<?php
namespace Jaspersoft\Service;

use Jaspersoft\Client\Client;
use Jaspersoft\Tool\Util;
use Jaspersoft\Dto\ImportExport\ExportTask;
use Jaspersoft\Dto\ImportExport\ImportTask;
use Jaspersoft\Dto\ImportExport\TaskState;

class ImportExportService
{
    protected $service;
    protected $restUrl2;

    public function __construct(Client &$client)
    {
        $this->service = $client->getService();
        $this->restUrl2 = $client->getURL();
    }

    /** This function begins an export task on the server. You must be authorized as a superuser to use these services
     *
     *
     * @param $et - ExportTask object defining the exporting you want to do
     * @return \Jaspersoft\Dto\ImportExport\TaskState
     */
    public function startExportTask(ExportTask $et)
    {
        $url = $this->restUrl2 . '/export';
        $json_data = $et->toJSON();
        $data = $this->service->prepAndSend($url, array(200), 'POST', $json_data, true, 'application/json', 'application/json');
        return TaskState::createFromJSON(json_decode($data));
    }

    /** Retrieve the state of your export request
     *
     *
     * @param $id - the ID of your export request
     * @return \Jaspersoft\Dto\ImportExport\TaskState
     */
    public function getExportState($id)
    {
        $url = $this->restUrl2 . '/export' . '/' . $id . '/state';
        $data = $this->service->prepAndSend($url, array(200), 'GET', null, true, 'application/json', 'application/json');
        return TaskState::createFromJSON(json_decode($data));
    }

    /**
     * Fetch the binary data of the report. This can only be called once before the server recycles the export request
     *
     * The filename parameter determines the headers sent by the server describing the file.
     *
     * @param $id
     * @param string $filename
     * @return string binary data
     */
    public function fetchExport($id, $filename = 'export.zip')
    {
        $url = $this->restUrl2 . '/export' . '/' . $id . '/' . $filename;
        $data = $this->service->prepAndSend($url, array(200), 'GET', null, true, 'application/json', 'application/zip');
        return $data;
    }

    /** Begin an import task
     *
     * @param $it ImportTask object defining the import to be done
     * @param $file_data - Binary contents of ZIP file you wish to upload. use file_get_contents() to produce from stored file
     * @return \Jaspersoft\Dto\ImportExport\TaskState
     */
    public function startImportTask(ImportTask $it, $file_data)
    {
        $url = $this->restUrl2 . '/import' . '?' . Util::query_suffix($it->queryData());
        $data = $this->service->prepAndSend($url, array(200, 201), 'POST', $file_data, true, 'application/zip', 'application/json');
        return TaskState::createFromJSON(json_decode($data));
    }

    /** Obtain the state of an ongoing import task
     *
     * @param $id
     * @return \Jaspersoft\Dto\ImportExport\TaskState
     */
    public function getImportState($id)
    {
        $url = $this->restUrl2 . '/import' . '/' . $id . '/state';
        $data = $this->service->prepAndSend($url, array(200), 'GET', null, true, 'application/json', 'application/json');
        return TaskState::createFromJSON(json_decode($data));
    }


}