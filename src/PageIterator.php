<?php
namespace DirectoryApp;

class PageIterator
{
	public $totalPages;
	public $startRow;

	// constructor
	public function __construct($rowsPerPage, $numRows, $currentPage = 1)
	{
		// calculate the total number of pages
		$this->totalPages = ceil($numRows / $rowsPerPage);

		// check that a valid page has been provided
		if($currentPage < 1) {
			$currentPage = 1;
		}

		else if($currentPage > $this->totalPages && $this->totalPages > 0) {
			$currentPage = $this->totalPages;
		}

		// calculate the row to start the select with
		$this->startRow = (($currentPage - 1) * $rowsPerPage);
	}

	//returns the total number of pages available
	public function getTotalPages()
	{
		return $this->totalPages;
	}

	// returns the row to start the select with
	public function getStartRow()
	{
		return $this->startRow;
	}
}