<?php
namespace Delorius\Forms\Controls;
use Delorius\Exception\Error;
use Delorius\Forms\Form;
use Delorius\Http\FileUpload;

/**
 * Text box and browse button that allow users to select a file to upload to the server.
 *
 *
 */
class UploadControl extends BaseControl
{

	/**
	 * @param  string  label
	 */
	public function __construct($label = NULL)
	{
		parent::__construct($label);
		$this->control->type = 'file';
	}



	/**
	 * This method will be called when the component (or component's parent)
	 * becomes attached to a monitored object. Do not call this method yourself.
	 * @param  Delorius\ComponentModel\IComponent
	 * @return void
	 */
	protected function attached($form)
	{
		if ($form instanceof \Delorius\Forms\Form) {
			if ($form->getMethod() !== Form::POST) {
				throw new Error('File upload requires method POST.');
			}
			$form->getElementPrototype()->enctype = 'multipart/form-data';
		}
		parent::attached($form);
	}



	/**
	 * Sets control's value.
	 * @param  array|\Delorius\Http\FileUpload
	 * @return \Delorius\Http\FileUpload  provides a fluent interface
	 */
	public function setValue($value)
	{
		if (is_array($value)) {
			$this->value = new FileUpload($value);

		} elseif ($value instanceof \Delorius\Http\FileUpload) {
			$this->value = $value;

		} else {
			$this->value = new FileUpload(NULL);
		}
		return $this;
	}



	/**
	 * Has been any file uploaded?
	 * @return bool
	 */
	public function isFilled()
	{
		return $this->value instanceof \Delorius\Http\FileUpload && $this->value->isOK();
	}



	/**
	 * FileSize validator: is file size in limit?
	 * @param  UploadControl
	 * @param  int  file size limit
	 * @return bool
	 */
	public static function validateFileSize(UploadControl $control, $limit)
	{
		$file = $control->getValue();
		return $file instanceof \Delorius\Http\FileUpload && $file->getSize() <= $limit;
	}



	/**
	 * MimeType validator: has file specified mime type?
	 * @param  UploadControl
	 * @param  array|string  mime type
	 * @return bool
	 */
	public static function validateMimeType(UploadControl $control, $mimeType)
	{
		$file = $control->getValue();
		if ($file instanceof \Delorius\Http\FileUpload) {
			$type = strtolower($file->getContentType());
			$mimeTypes = is_array($mimeType) ? $mimeType : explode(',', $mimeType);
			if (in_array($type, $mimeTypes, TRUE)) {
				return TRUE;
			}
			if (in_array(preg_replace('#/.*#', '/*', $type), $mimeTypes, TRUE)) {
				return TRUE;
			}
		}
		return FALSE;
	}



	/**
	 * Image validator: is file image?
	 * @return bool
	 */
	public static function validateImage(UploadControl $control)
	{
		$file = $control->getValue();
		return $file instanceof \Delorius\Http\FileUpload && $file->isImage();
	}

}
