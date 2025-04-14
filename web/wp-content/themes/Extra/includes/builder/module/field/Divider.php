<?php
/**
 * Module Divider class.
 */
class ET_Builder_Module_Field_Divider extends ET_Builder_Module_Field_Base {
	/**
	 * List of available dividers for the sections.
	 *
	 * @var array
	 */
	public $dividers = array();

	/**
	 * Markup for the SVG
	 *
	 * @var string
	 */
	public $svg;

	/**
	 * List of classes for using in styling.
	 *
	 * @var array
	 */
	public $classes = array( 'section_has_divider' );

	/**
	 * @var ET_Core_Data_Utils
	 */
	public static $data_utils = null;

	/**
	 * @var ET_Builder_Module_Helper_ResponsiveOptions
	 *
	 * @since 3.23
	 */
	public static $responsive = null;

	/**
	 * Number of times the module has been rendered.
	 *
	 * @var int
	 */
	public $count;

	/**
	 * Constructor for the class. This is done so that the divider options could be filtered
	 * by a child theme or plugin.
	 */
	public function __construct() {

		$section_dividers = array(
			'arrow-bottom'       => '<path d="M640 139L0 0v140h1280V0L640 139z"/>',
			'arrow-top'          => '<path d="M640 140L1280 0H0z"/>',
			'arrow2-bottom'      => '<path d="M640 139L0 0v140h1280V0L640 139z" fill-opacity=".5"/><path d="M640 139L0 42v98h1280V42l-640 97z"/>',
			'arrow2-top'         => '<path d="M640 140L1280 0H0z" fill-opacity=".5"/><path d="M640 98l640-98H0z"/>',
			'arrow3-bottom'      => '<path d="M0 140l640-70 640 70V0L640 70 0 0v140z" fill-opacity=".5"/><path d="M0 140h1280L640 70 0 140z"/>',
			'arrow3-top'         => '<path d="M1280 0L640 70 0 0v140l640-70 640 70V0z" fill-opacity=".5"/><path d="M1280 0H0l640 70 640-70z"/>',
			'asymmetric-bottom'  => '<path d="M1280 0l-262.1 116.26a73.29 73.29 0 0 1-39.09 6L0 0v140h1280z"/>',
			'asymmetric-top'     => '<path d="M978.81 122.25L0 0h1280l-262.1 116.26a73.29 73.29 0 0 1-39.09 5.99z"/>',
			'asymmetric2-bottom' => '<path d="M1280 0l-266 91.52a72.59 72.59 0 0 1-30.76 3.71L0 0v140h1280z" fill-opacity=".5"/><path d="M1280 0l-262.1 116.26a73.29 73.29 0 0 1-39.09 6L0 0v140h1280z"/>',
			'asymmetric2-top'    => '<path d="M978.81 122.25L0 0h1280l-262.1 116.26a73.29 73.29 0 0 1-39.09 5.99z" fill-opacity=".5"/><path d="M983.19 95.23L0 0h1280l-266 91.52a72.58 72.58 0 0 1-30.81 3.71z"/>',
			'asymmetric3-bottom' => '<path d="M1093.48 131.85L173 94a76.85 76.85 0 0 1-36.79-11.46L0 0v140h1280V0l-131.81 111.68c-16.47 13.96-35.47 20.96-54.71 20.17z"/>',
			'asymmetric3-top'    => '<path d="M1280 0l-131.81 111.68c-16.47 14-35.47 21-54.71 20.17L173 94a76.85 76.85 0 0 1-36.79-11.46L0 0z"/>',
			'asymmetric4-bottom' => '<path d="M1094.44 119L172.7 68.72a74.54 74.54 0 0 1-25.19-5.95L0 0v140h1280V0l-133.85 102c-15.84 12.09-33.7 17.95-51.71 17z" fill-opacity=".5"/><path d="M1093.48 131.85L173 94a76.85 76.85 0 0 1-36.79-11.46L0 0v140h1280V0l-131.81 111.68c-16.47 13.96-35.47 20.96-54.71 20.17z"/>',
			'asymmetric4-top'    => '<path d="M1093.48 131.85L173 94a76.85 76.85 0 0 1-36.79-11.46L0 0h1280l-131.81 111.68c-16.47 13.96-35.47 20.96-54.71 20.17z" fill-opacity=".5"/><path d="M1094.44 119L172.7 68.72a74.54 74.54 0 0 1-25.19-5.95L0 0h1280l-133.85 102c-15.84 12.09-33.7 17.95-51.71 17z"/>',
			'clouds-bottom'      => '<path d="M1280 63.1c-3.8 0-7.6.3-11.4.8-18.3-32.6-59.6-44.2-92.2-25.9-3.5 2-6.9 4.3-10 6.9-22.7-41.7-74.9-57.2-116.6-34.5-14.2 7.7-25.9 19.3-33.8 33.3-.2.3-.3.6-.5.8-12.2-1.4-23.7 5.9-27.7 17.5-11.9-6.1-25.9-6.3-37.9-.6-21.7-30.4-64-37.5-94.4-15.8-12.1 8.6-21 21-25.4 35.2-10.8-9.3-24.3-15-38.5-16.2-8.1-24.6-34.6-38-59.2-29.9-14.3 4.7-25.5 16-30 30.3-4.3-1.9-8.9-3.2-13.6-3.8-13.6-45.2-61.5-71.1-107-57.6A86.38 86.38 0 0 0 538.6 33c-8.7-3.6-18.7-1.8-25.4 4.8-23.1-24.8-61.9-26.2-86.7-3.1-7.1 6.6-12.5 14.8-15.9 24-26.7-10.1-56.9-.4-72.8 23.3-2.6-2.7-5.6-5.1-8.9-6.9-.4-.2-.8-.4-1.2-.7-.6-25.9-22-46.4-47.9-45.8-11.5.3-22.5 4.7-30.9 12.5-16.5-33.5-57-47.4-90.5-31-22 10.8-36.4 32.6-37.8 57.1-7-2.3-14.5-2.8-21.8-1.6-14-21.7-43.1-27.9-64.8-13.8-5.6 3.6-10.3 8.4-13.9 14C13.5 64 6.8 63.2 0 63.2-.1 63.2 0 86 0 86h1280V63.1z"/>',
			'clouds-bottom2'     => '<path d="M1280,63.1a81.42,81.42,0,0,0-11.41.81,67.71,67.71,0,0,0-102.21-19,86,86,0,0,0-150.47-1.2c-.16.28-.29.57-.45.85a26.07,26.07,0,0,0-27.65,17.54,43,43,0,0,0-37.93-.57A67.66,67.66,0,0,0,830.15,81a67.85,67.85,0,0,0-38.51-16.19,46.9,46.9,0,0,0-89.25.45,46.66,46.66,0,0,0-13.56-3.77A86,86,0,0,0,538.67,33.07a23.42,23.42,0,0,0-25.4,4.8A61.36,61.36,0,0,0,410.7,58.74a61.44,61.44,0,0,0-72.79,23.32A38.37,38.37,0,0,0,329,75.15c-.41-.23-.83-.45-1.25-.66a46.88,46.88,0,0,0-78.77-33.31A67.65,67.65,0,0,0,120.71,67.29a46.76,46.76,0,0,0-21.82-1.62,46.91,46.91,0,0,0-78.8.07A79.35,79.35,0,0,0,0,63.17C0,63.17,0,140,0,140H1280Z"/>',
			'clouds-top'         => '<path d="M1280 0H0v65.2c6.8 0 13.5.9 20.1 2.6 14-21.8 43.1-28 64.8-14 5.6 3.6 10.3 8.3 14 13.9 7.3-1.2 14.8-.6 21.8 1.6 2.1-37.3 34.1-65.8 71.4-63.7 24.3 1.4 46 15.7 56.8 37.6 19-17.6 48.6-16.5 66.3 2.4C323 54 327.4 65 327.7 76.5c.4.2.8.4 1.2.7 3.3 1.9 6.3 4.2 8.9 6.9 15.9-23.8 46.1-33.4 72.8-23.3 11.6-31.9 46.9-48.3 78.8-36.6 9.1 3.3 17.2 8.7 23.8 15.7 6.7-6.6 16.7-8.4 25.4-4.8 29.3-37.4 83.3-44 120.7-14.8 14 11 24.3 26.1 29.4 43.1 4.7.6 9.3 1.8 13.6 3.8 7.8-24.7 34.2-38.3 58.9-30.5 14.4 4.6 25.6 15.7 30.3 30 14.2 1.2 27.7 6.9 38.5 16.2 11.1-35.7 49-55.7 84.7-44.7 14.1 4.4 26.4 13.3 35 25.3 12-5.7 26.1-5.5 37.9.6 3.9-11.6 15.5-18.9 27.7-17.5.2-.3.3-.6.5-.9 23.3-41.4 75.8-56 117.2-32.6 14.1 7.9 25.6 19.7 33.3 33.8 28.8-23.8 71.5-19.8 95.3 9 2.6 3.1 4.9 6.5 6.9 10 3.8-.5 7.6-.8 11.4-.8L1280 0z"/>',
			'clouds-top2'        => '<path d="M1280,0H0S0,116.17,0,116.17a79.47,79.47,0,0,1,20.07,2.57,46.91,46.91,0,0,1,78.8-.07,46.76,46.76,0,0,1,21.82,1.62A67.67,67.67,0,0,1,248.93,94.17a46.88,46.88,0,0,1,78.77,33.31c.42.22.84.43,1.25.66a38.38,38.38,0,0,1,8.94,6.92,61.44,61.44,0,0,1,72.79-23.32A61.43,61.43,0,0,1,513.26,90.87a23.42,23.42,0,0,1,25.4-4.8,86,86,0,0,1,150.15,28.37,46.65,46.65,0,0,1,13.56,3.77,46.9,46.9,0,0,1,89.25-.45A67.85,67.85,0,0,1,830.13,134a67.7,67.7,0,0,1,119.73-19.38,43,43,0,0,1,37.93.57,26.07,26.07,0,0,1,27.65-17.54c.16-.28.29-.57.45-.85A86,86,0,0,1,1166.37,98a67.71,67.71,0,0,1,102.21,19,81.66,81.66,0,0,1,11.42-.81Z"/>',
			'clouds2-bottom'     => '<path d="M1280 66.1c-3.8 0-7.6.3-11.4.8-18.3-32.6-59.6-44.2-92.2-25.9-3.5 2-6.9 4.3-10 6.9-22.7-41.7-74.9-57.2-116.6-34.5-14.2 7.7-25.9 19.3-33.8 33.3-.2.3-.3.6-.5.8-12.2-1.4-23.7 5.9-27.7 17.5-11.9-6.1-25.9-6.3-37.9-.6-21.7-30.4-64-37.5-94.4-15.7-12.1 8.6-21 21-25.4 35.2-10.8-9.3-24.3-15-38.5-16.2-8.1-24.6-34.6-38-59.2-29.9-14.3 4.7-25.5 16-30 30.3-4.3-1.9-8.9-3.2-13.6-3.8-13.6-45.5-61.5-71.4-107-57.8a86.38 86.38 0 0 0-43.2 29.4c-8.7-3.6-18.7-1.8-25.4 4.8-23.1-24.8-61.9-26.2-86.7-3.1-7.1 6.6-12.5 14.8-15.9 24-26.7-10.1-56.9-.4-72.8 23.3-2.6-2.7-5.6-5.1-8.9-6.9-.4-.2-.8-.4-1.2-.7-.6-25.9-22-46.4-47.9-45.8-11.5.3-22.5 4.7-30.9 12.5-16.5-33.5-57.1-47.3-90.6-30.8-21.9 11-36.3 32.7-37.6 57.1-7-2.3-14.5-2.8-21.8-1.6C84.8 47 55.7 40.7 34 54.8c-5.6 3.6-10.3 8.4-13.9 14-6.6-1.7-13.3-2.6-20.1-2.6-.1 0 0 19.8 0 19.8h1280V66.1z" fill-opacity=".5"/><path d="M15.6 86H1280V48.5c-3.6 1.1-7.1 2.5-10.4 4.4-6.3 3.6-11.8 8.5-16 14.5-8.1-1.5-16.4-.9-24.2 1.7-3.2-39-37.3-68.1-76.4-64.9-24.8 2-46.8 16.9-57.9 39.3-19.9-18.5-51-17.3-69.4 2.6-8.2 8.8-12.8 20.3-13.1 32.3-.4.2-.9.4-1.3.7-3.5 1.9-6.6 4.4-9.4 7.2-16.6-24.9-48.2-35-76.2-24.4-12.2-33.4-49.1-50.6-82.5-38.4-9.5 3.5-18.1 9.1-25 16.5-7.1-6.9-17.5-8.8-26.6-5-30.4-39.3-87-46.3-126.2-15.8-14.8 11.5-25.6 27.4-31 45.4-4.9.6-9.7 1.9-14.2 3.9-8.2-25.9-35.8-40.2-61.7-32-15 4.8-26.9 16.5-31.8 31.5-14.9 1.3-29 7.2-40.3 17-11.5-37.4-51.2-58.4-88.7-46.8-14.8 4.6-27.7 13.9-36.7 26.5-12.6-6-27.3-5.7-39.7.6-4.1-12.2-16.2-19.8-29-18.4-.2-.3-.3-.6-.5-.9-24.4-43.3-79.4-58.6-122.7-34.2-13.3 7.5-24.4 18.2-32.4 31.2C99.8 18.5 50 28.5 25.4 65.4c-4.3 6.4-7.5 13.3-9.8 20.6z"/>',
			'clouds2-bottom2'    => '<path d="M1269.61,52.83a48.82,48.82,0,0,0-16,14.48A48.6,48.6,0,0,0,1229.45,69a70.88,70.88,0,0,0-134.21-25.66,49.11,49.11,0,0,0-82.51,34.9c-.44.23-.88.45-1.31.69a40.18,40.18,0,0,0-9.36,7.24,64.35,64.35,0,0,0-76.25-24.43A64.34,64.34,0,0,0,818.36,39.85a24.53,24.53,0,0,0-26.61-5A90,90,0,0,0,634.48,64.55a48.89,48.89,0,0,0-14.21,3.95A49.12,49.12,0,0,0,526.79,68a71.07,71.07,0,0,0-40.34,17A70.91,70.91,0,0,0,361,64.68a45.07,45.07,0,0,0-39.73.6,27.31,27.31,0,0,0-29-18.37c-.16-.29-.31-.59-.47-.89a90.06,90.06,0,0,0-155.12-3A80.23,80.23,0,0,0,12.64,99.75a80.1,80.1,0,0,0-12.64,2V140H1280V48.48A49.22,49.22,0,0,0,1269.61,52.83Z" fill-opacity=".5"/><path d="M1280,66.1a81.63,81.63,0,0,0-11.42.81,67.71,67.71,0,0,0-102.21-19,86,86,0,0,0-150.47-1.2c-.16.28-.29.57-.45.85a26.07,26.07,0,0,0-27.65,17.54,43,43,0,0,0-37.93-.57A67.66,67.66,0,0,0,830.13,84a67.85,67.85,0,0,0-38.51-16.19,46.9,46.9,0,0,0-89.25.45,46.66,46.66,0,0,0-13.56-3.77A86,86,0,0,0,538.66,36.07a23.42,23.42,0,0,0-25.4,4.8A61.36,61.36,0,0,0,410.68,61.74a61.44,61.44,0,0,0-72.79,23.32A38.37,38.37,0,0,0,329,78.15c-.41-.23-.83-.45-1.25-.66a46.88,46.88,0,0,0-78.77-33.31A67.65,67.65,0,0,0,120.69,70.29a46.76,46.76,0,0,0-21.82-1.62,46.91,46.91,0,0,0-78.8.07A79.46,79.46,0,0,0,0,66.17C-.07,66.17,0,140,0,140H1280Z"/>',
			'clouds2-top'        => '<path d="M833.9 27.5c-5.8 3.2-11 7.3-15.5 12.2-7.1-6.9-17.5-8.8-26.6-5-30.6-39.2-87.3-46.1-126.5-15.5-1.4 1.1-2.8 2.2-4.1 3.4C674.4 33.4 684 48 688.8 64.3c4.7.6 9.3 1.8 13.6 3.8 7.8-24.7 34.2-38.3 58.9-30.5 14.4 4.6 25.6 15.7 30.3 30 14.2 1.2 27.7 6.9 38.5 16.2C840.6 49.6 876 29.5 910.8 38c-20.4-20.3-51.8-24.6-76.9-10.5zM384 43.9c-9 5-16.7 11.9-22.7 20.3 15.4-7.8 33.3-8.7 49.4-2.6 3.7-10.1 9.9-19.1 18.1-26-15.4-2.3-31.2.6-44.8 8.3zm560.2 13.6c2 2.2 3.9 4.5 5.7 6.9 5.6-2.6 11.6-4 17.8-4.1-7.6-2.4-15.6-3.3-23.5-2.8zM178.7 7c29-4.2 57.3 10.8 70.3 37 8.9-8.3 20.7-12.8 32.9-12.5C256.4 1.8 214.7-8.1 178.7 7zm146.5 56.3c1.5 4.5 2.4 9.2 2.5 14 .4.2.8.4 1.2.7 3.3 1.9 6.3 4.2 8.9 6.9 5.8-8.7 13.7-15.7 22.9-20.5-11.1-5.2-23.9-5.6-35.5-1.1zM33.5 54.9c21.6-14.4 50.7-8.5 65 13 .1.2.2.3.3.5 7.3-1.2 14.8-.6 21.8 1.6.6-10.3 3.5-20.4 8.6-29.4.3-.6.7-1.2 1.1-1.8-32.1-17.2-71.9-10.6-96.8 16.1zm1228.9 2.7c2.3 2.9 4.4 5.9 6.2 9.1 3.8-.5 7.6-.8 11.4-.8V48.3c-6.4 1.8-12.4 5-17.6 9.3zM1127.3 11c1.9.9 3.7 1.8 5.6 2.8 14.2 7.9 25.8 19.7 33.5 34 13.9-11.4 31.7-16.9 49.6-15.3-20.5-27.7-57.8-36.8-88.7-21.5z" fill-opacity=".5"/><path d="M0 0v66c6.8 0 13.5.9 20.1 2.6 3.5-5.4 8.1-10.1 13.4-13.6 24.9-26.8 64.7-33.4 96.8-16 10.5-17.4 28.2-29.1 48.3-32 36.1-15.1 77.7-5.2 103.2 24.5 19.7.4 37.1 13.1 43.4 31.8 11.5-4.5 24.4-4.2 35.6 1.1l.4-.2c15.4-21.4 41.5-32.4 67.6-28.6 25-21 62.1-18.8 84.4 5.1 6.7-6.6 16.7-8.4 25.4-4.8 29.2-37.4 83.3-44.1 120.7-14.8l1.8 1.5c37.3-32.9 94.3-29.3 127.2 8 1.2 1.3 2.3 2.7 3.4 4.1 9.1-3.8 19.5-1.9 26.6 5 24.3-26 65-27.3 91-3.1.5.5 1 .9 1.5 1.4 12.8 3.1 24.4 9.9 33.4 19.5 7.9-.5 15.9.4 23.5 2.8 7-.1 13.9 1.5 20.1 4.7 3.9-11.6 15.5-18.9 27.7-17.5.2-.3.3-.6.5-.9 22.1-39.2 70.7-54.7 111.4-35.6 30.8-15.3 68.2-6.2 88.6 21.5 18.3 1.7 35 10.8 46.5 25.1 5.2-4.3 11.1-7.4 17.6-9.3V0H0z"/>',
			'clouds2-top2'       => '<path d="M833.9,77.67a64.2,64.2,0,0,0-15.53,12.18,24.53,24.53,0,0,0-26.61-5,90.1,90.1,0,0,0-130.57-12.1,85.54,85.54,0,0,1,27.62,41.73,46.66,46.66,0,0,1,13.56,3.77,46.9,46.9,0,0,1,89.25-.45A67.84,67.84,0,0,1,830.13,134,67.61,67.61,0,0,1,910.8,88.2,64.38,64.38,0,0,0,833.9,77.67Z M384,94.11a70.48,70.48,0,0,0-22.73,20.27,61.44,61.44,0,0,1,49.42-2.63,61.19,61.19,0,0,1,18.15-26A70.64,70.64,0,0,0,384,94.11Z M944.2,107.71a67.34,67.34,0,0,1,5.66,6.87,43.3,43.3,0,0,1,17.8-4.1A64.53,64.53,0,0,0,944.2,107.71Z M178.67,57.16a67.63,67.63,0,0,1,70.26,37,47.06,47.06,0,0,1,32.92-12.5A90.18,90.18,0,0,0,178.67,57.16Z M325.24,113.52a46.93,46.93,0,0,1,2.46,14c.42.22.84.43,1.25.66a38.38,38.38,0,0,1,8.94,6.92,61,61,0,0,1,22.94-20.48A45.09,45.09,0,0,0,325.24,113.52Z M33.49,105.13a46.91,46.91,0,0,1,65.38,13.55,46.75,46.75,0,0,1,21.82,1.62,67.13,67.13,0,0,1,8.58-29.39c.34-.6.7-1.19,1.06-1.78a80.19,80.19,0,0,0-96.84,16Z M1262.42,107.77a67.35,67.35,0,0,1,6.16,9.15,81.66,81.66,0,0,1,11.42-.81V98.48a48.83,48.83,0,0,0-17.58,9.29Z M1127.33,61.18c1.88.88,3.74,1.81,5.58,2.84A85.42,85.42,0,0,1,1166.37,98a68,68,0,0,1,49.55-15.27A70.94,70.94,0,0,0,1127.33,61.18Z" fill-opacity=".5"/><path d="M361,114.68l.23-.3-.43.22Z M0,0V120.87c0-3,0-4.69,0-4.69a79.35,79.35,0,0,1,20.06,2.57,46.56,46.56,0,0,1,13.42-13.62,80.19,80.19,0,0,1,96.84-16,67.52,67.52,0,0,1,48.33-32A90.18,90.18,0,0,1,281.84,81.67a46.82,46.82,0,0,1,43.4,31.85,45.09,45.09,0,0,1,35.59,1.07l.43-.22a70.84,70.84,0,0,1,67.57-28.6,61.47,61.47,0,0,1,84.42,5.09,23.42,23.42,0,0,1,25.4-4.8A86,86,0,0,1,661.19,72.72a90.1,90.1,0,0,1,130.57,12.1,24.53,24.53,0,0,1,26.61,5A64.37,64.37,0,0,1,910.8,88.2a67.45,67.45,0,0,1,33.4,19.51,64.53,64.53,0,0,1,23.45,2.77,42.8,42.8,0,0,1,20.14,4.67,26.07,26.07,0,0,1,27.65-17.54c.16-.28.29-.57.45-.85a86,86,0,0,1,111.44-35.58,70.94,70.94,0,0,1,88.59,21.52,67.79,67.79,0,0,1,46.5,25.07A48.83,48.83,0,0,1,1280,98.48V0Z"/>',
			'curve-bottom'       => '<path d="M1280 140V0S993.46 140 640 139 0 0 0 0v140z"/>',
			'curve-top'          => '<path d="M640 140C286.54 140 0 0 0 0h1280S993.46 140 640 140z"/>',
			'curve2-bottom'      => '<path d="M725.29 101.2C325.22 122.48 0 0 0 0v140h1280V0s-154.64 79.92-554.71 101.2z" fill-opacity=".3"/><path d="M556.45 119.74C953.41 140 1280 14 1280 14v126H0V0s159.5 99.48 556.45 119.74z" fill-opacity=".5"/><path d="M640 140c353.46 0 640-140 640-139v140H0V0s286.54 140 640 140z"/>',
			'curve2-top'         => '<path d="M0 0v.48C18.62 9.38 297.81 140 639.5 140 993.24 140 1280 0 1280 0z" fill-opacity=".3"/><path d="M0 .6c14 8.28 176.54 99.8 555.45 119.14C952.41 140 1280 0 1280 0H0z" fill-opacity=".5"/><path d="M726.29 101.2C1126.36 79.92 1281 0 1281 0H1c.05 0 325.25 122.48 725.29 101.2z"/>',
			'graph-bottom'       => '<path d="M0 122.138l60.614 9.965 95.644-4.2 86.363-18.654 78.684 13.079L411.442 99.4l94.453 10.303L582.821 93.8l82.664 18.728 76.961-11.39L816.11 71.4l97.601 9.849L997.383 50.4l66.285 14.694 70.793-24.494h79.863L1280 0v140H0z"/>',
			'graph-top'          => '<path d="M156.258 127.903l86.363-18.654 78.684 13.079L411.441 99.4l94.454 10.303L582.82 93.8l82.664 18.728 76.961-11.39L816.109 71.4l97.602 9.849L997.383 50.4l66.285 14.694 70.793-24.494h79.863L1280 0H0v122.138l60.613 9.965z"/>',
			'graph2-bottom'      => '<path d="M0 127.899l60.613 4.878 95.645-6.211 86.363-16.074 78.684 9.883 90.136-21.594 94.454 7.574 77.925-17.66 91.664 20.798 76.961-12.649 63.664-21.422 97.602 7.07 83.672-29.617 66.285 11.678 70.793-23.334 74.863-4.641L1280 0v140H0z" fill-opacity=".5"/><path d="M0 126.71l60.613 7.415L156.257 131l86.364-13.879 78.683 9.731 90.137-17.059 94.453 7.666 76.926-11.833 82.664 13.935 76.961-8.475 73.664-22.126 97.601 7.328 83.672-22.952 66.285 10.933 70.794-18.224h79.862L1280 35.838V140H0z"/>',
			'graph2-top'         => '<path d="M1214.323 66.051h-79.863l-70.793 18.224-66.285-10.933-83.672 22.953-97.601-7.328-73.664 22.125-76.961 8.475-82.664-13.934-76.926 11.832-94.453-7.666-90.137 17.059-78.684-9.731-86.363 13.879-95.644 3.125L0 126.717V0h1280l-.001 35.844z" fill-opacity=".5"/><path d="M0 0h1280v.006l-70.676 36.578-74.863 4.641-70.793 23.334-66.285-11.678-83.672 29.618-97.602-7.07-63.664 21.421-76.961 12.649-91.664-20.798-77.926 17.66-94.453-7.574-90.137 21.595-78.683-9.884-86.363 16.074-95.645 6.211L0 127.905z"/>',
			'graph3-bottom'      => '<path d="M0 0l64.8 38.69 91.2-3.18 95.45 34.84 120.04.24 71.5 33.35 90.08-3.91 106.91 37.62 102.38-37.17 85.55 10.65 88.11-7.19 75.95-38.66 73.21 5.31 66.78-22.1 77-.42 71-48.07v140H0V0z"/>',
			'graph3-top'         => '<path d="M156 35.51l95.46 34.84 120.04.24 71.5 33.35 90.09-3.91L640 137.65l102.39-37.17 85.55 10.65 88.11-7.19L992 65.28l73.21 5.31 66.79-22.1 77-.42L1280 0H0l64.8 38.69 91.2-3.18z"/>',
			'graph4-bottom'      => '<path d="M0 0l64.8 30.95 91.2-2.54 95.46 27.87 120.04.2L443 83.15l90.09-3.12L640 110.12l102.39-29.73 85.55 8.51 88.11-5.75L992 52.22l73.21 4.26L1132 38.79l77-.33L1280 0v140H0V0z" fill-opacity=".5"/><path d="M0 0l64.8 38.69 91.2-3.18 95.46 34.84 120.04.24 71.5 33.35 90.09-3.91L640 137.65l102.39-37.17 85.55 10.65 88.11-7.19L992 65.28l73.21 5.31 66.79-22.1 77-.41L1280 0v140H0V0z"/>',
			'graph4-top'         => '<path d="M156 35.41l95.46 34.73 120.04.25 71.5 33.24 90.09-3.89L640 137.25l102.39-37.06 85.55 10.61 88.11-7.17L992 65.08l73.21 5.31L1132 48.35l77-.42L1280 0H0l64.8 38.57 91.2-3.16z" fill-opacity=".5"/><path d="M156 28.32l95.46 27.79 120.04.2L443 82.9l90.09-3.11L640 109.8l102.39-29.65 85.55 8.49 88.11-5.74L992 52.07l73.21 4.24L1132 38.68l77-.34L1280 0H0l64.8 30.86 91.2-2.54z"/>',
			'mountains-bottom'   => '<path d="M0 70.35l320-49.24 640 98.49 320-49.25V140H0V70.35z"/>',
			'mountains-top'      => '<path d="M1280 69.65l-320 49.24L320 20.4 0 69.65V0h1280v69.65z"/>',
			'mountains2-bottom'  => '<path d="M0 47.44L170 0l626.48 94.89L1110 87.11l170-39.67V140H0V47.44z" fill-opacity=".5"/><path d="M0 90.72l140-28.28 315.52 24.14L796.48 65.8 1140 104.89l140-14.17V140H0V90.72z"/>',
			'mountains2-top'     => '<path d="M0 90.72l140-28.28 315.52 24.14L796.48 65.8 1140 104.89l140-14.17V0H0v90.72z" fill-opacity=".5"/><path d="M0 0v47.44L170 0l626.48 94.89L1110 87.11l170-39.67V0H0z"/>',
			'ramp-bottom'        => '<path d="M0 140h1280C573.08 140 0 0 0 0z"/>',
			'ramp-top'           => '<path d="M0 0s573.08 140 1280 140V0z"/>',
			'ramp2-bottom'       => '<path d="M0 140h1280C573.08 140 0 0 0 0z" fill-opacity=".3"/><path d="M0 140h1280C573.08 140 0 30 0 30z" fill-opacity=".5"/><path d="M0 140h1280C573.08 140 0 60 0 60z"/>',
			'ramp2-top'          => '<path d="M0 0v60s573.09 80 1280 80V0z" fill-opacity=".3"/><path d="M0 0v30s573.09 110 1280 110V0z" fill-opacity=".5"/><path d="M0 0s573.09 140 1280 140V0z"/>',
			'slant-bottom'       => '<path d="M0 0v140h1280L0 0z"/>',
			'slant-top'          => '<path d="M1280 140V0H0l1280 140z"/>',
			'slant2-bottom'      => '<path d="M0 0v140h1280L0 0z" fill-opacity=".5"/><path d="M0 42v98h1280L0 42z"/>',
			'slant2-top'         => '<path d="M1280 140V0H0l1280 140z" fill-opacity=".5"/><path d="M1280 98V0H0l1280 98z"/>',
			'triangle-bottom'    => '<path d="M80 0L0 140h160z"/>',
			'triangle-bottom2'   => '<polygon points="640 0 560 140 720 140 640 0"/>',
			'triangle-top'       => '<path d="M720 140L640 0l-80 140H0V0h1280v140H720z"/>',
			'triangle-top2'      => '<path d="M720 140L640 0l-80 140H0V0h1280v140H720z"/>',
			'wave-bottom'        => '<path d="M320 28c320 0 320 84 640 84 160 0 240-21 320-42v70H0V70c80-21 160-42 320-42z"/>',
			'wave-top'           => '<path d="M320 28C160 28 80 49 0 70V0h1280v70c-80 21-160 42-320 42-320 0-320-84-640-84z"/>',
			'wave2-bottom'       => '<path d="M1280 3.4C1050.59 18 1019.4 84.89 734.42 84.89c-320 0-320-84.3-640-84.3C59.4.59 28.2 1.6 0 3.4V140h1280z" fill-opacity=".3"/><path d="M0 24.31c43.46-5.69 94.56-9.25 158.42-9.25 320 0 320 89.24 640 89.24 256.13 0 307.28-57.16 481.58-80V140H0z" fill-opacity=".5"/><path d="M1280 51.76c-201 12.49-242.43 53.4-513.58 53.4-320 0-320-57-640-57-48.85.01-90.21 1.35-126.42 3.6V140h1280z"/>',
			'wave2-top'          => '<path d="M0 51.76c36.21-2.25 77.57-3.58 126.42-3.58 320 0 320 57 640 57 271.15 0 312.58-40.91 513.58-53.4V0H0z" fill-opacity=".3"/><path d="M0 24.31c43.46-5.69 94.56-9.25 158.42-9.25 320 0 320 89.24 640 89.24 256.13 0 307.28-57.16 481.58-80V0H0z" fill-opacity=".5"/><path d="M0 0v3.4C28.2 1.6 59.4.59 94.42.59c320 0 320 84.3 640 84.3 285 0 316.17-66.85 545.58-81.49V0z"/>',
			'waves-bottom'       => '<path d="M1280 86c-19.9-17.21-40.08-39.69-79.89-39.69-57.49 0-56.93 46.59-115 46.59-53.61 0-59.76-39.62-115.6-39.62C923.7 53.27 924.26 87 853.89 87c-89.35 0-78.74-87-188.2-87C554 0 543.95 121.8 423.32 121.8c-100.52 0-117.84-54.88-191.56-54.88-77.06 0-100 48.57-151.75 48.57-40 0-60-12.21-80-29.51v54H1280z"/>',
			'waves-top'          => '<path d="M0 0v100c20 17.3 40 29.51 80 29.51 51.79 0 74.69-48.57 151.75-48.57 73.72 0 91 54.88 191.56 54.88C543.95 135.8 554 14 665.69 14c109.46 0 98.85 87 188.2 87 70.37 0 69.81-33.73 115.6-33.73 55.85 0 62 39.62 115.6 39.62 58.08 0 57.52-46.59 115-46.59 39.8 0 60 22.48 79.89 39.69V0z"/>',
			'waves2-bottom'      => '<path d="M853.893,86.998c-38.859,0-58.811-16.455-77.956-35.051c18.295-10.536,40.891-18.276,73.378-18.276 c38.685,0,64.132,12.564,85.489,28.347C916.192,72.012,900.8,86.998,853.893,86.998z M526.265,80.945 c-6.517-0.562-13.599-0.879-21.41-0.879c-70.799,0-91.337,27.229-134.433,35.662c14.901,3.72,32.118,6.07,52.898,6.07 C470.171,121.797,500.34,103.421,526.265,80.945z" fill-opacity=".3"/><path d="M663.458,109.671c-67.137,0-80.345-23.824-137.193-28.726C567.086,45.555,597.381,0,665.691,0 c61.857,0,85.369,27.782,110.246,51.947C736.888,74.434,717.459,109.671,663.458,109.671z M217.68,94.163 c55.971,0,62.526,24.026,126.337,24.026c9.858,0,18.508-0.916,26.404-2.461c-57.186-14.278-80.177-48.808-138.659-48.808 c-77.063,0-99.96,48.569-151.751,48.569c-40.006,0-60.008-12.206-80.011-29.506v16.806c20.003,10.891,40.005,21.782,80.011,21.782 C160.014,124.57,158.608,94.163,217.68,94.163z M1200.112,46.292c-57.493,0-56.935,46.595-115.015,46.595 c-53.612,0-59.755-39.618-115.602-39.618c-15.267,0-25.381,3.751-34.69,8.749c36.096,26.675,60.503,62.552,117.342,62.552 c69.249,0,75.951-43.559,147.964-43.559c39.804,0,59.986,10.943,79.888,21.777V85.982 C1260.097,68.771,1239.916,46.292,1200.112,46.292z" fill-opacity=".5"/><path d="M1052.147,124.57c-56.84,0-81.247-35.876-117.342-62.552c-18.613,9.994-34.005,24.98-80.912,24.98 c-38.859,0-58.811-16.455-77.956-35.051c-39.05,22.487-58.479,57.724-112.48,57.724c-67.137,0-80.345-23.824-137.193-28.726 c-25.925,22.475-56.093,40.852-102.946,40.852c-20.779,0-37.996-2.349-52.898-6.07c-7.895,1.545-16.546,2.461-26.404,2.461 c-63.811,0-70.366-24.026-126.337-24.026c-59.072,0-57.665,30.407-137.669,30.407c-40.006,0-60.008-10.891-80.011-21.782V140h1280 v-37.212c-19.903-10.835-40.084-21.777-79.888-21.777C1128.098,81.011,1121.397,124.57,1052.147,124.57z"/>',
			'waves2-top'         => '<path d="M504.854,80.066c7.812,0,14.893,0.318,21.41,0.879 c-25.925,22.475-56.093,40.852-102.946,40.852c-20.779,0-37.996-2.349-52.898-6.07C413.517,107.295,434.056,80.066,504.854,80.066z M775.938,51.947c19.145,18.596,39.097,35.051,77.956,35.051c46.907,0,62.299-14.986,80.912-24.98 c-21.357-15.783-46.804-28.348-85.489-28.348C816.829,33.671,794.233,41.411,775.938,51.947z" fill-opacity=".3"/><path d="M1200.112,46.292c39.804,0,59.986,22.479,79.888,39.69v16.805 c-19.903-10.835-40.084-21.777-79.888-21.777c-72.014,0-78.715,43.559-147.964,43.559c-56.84,0-81.247-35.876-117.342-62.552 c9.309-4.998,19.423-8.749,34.69-8.749c55.846,0,61.99,39.617,115.602,39.617C1143.177,92.887,1142.618,46.292,1200.112,46.292z M80.011,115.488c-40.006,0-60.008-12.206-80.011-29.506v16.806c20.003,10.891,40.005,21.782,80.011,21.782 c80.004,0,78.597-30.407,137.669-30.407c55.971,0,62.526,24.026,126.337,24.026c9.858,0,18.509-0.916,26.404-2.461 c-57.186-14.278-80.177-48.808-138.66-48.808C154.698,66.919,131.801,115.488,80.011,115.488z M526.265,80.945 c56.848,4.902,70.056,28.726,137.193,28.726c54.001,0,73.43-35.237,112.48-57.724C751.06,27.782,727.548,0,665.691,0 C597.381,0,567.086,45.555,526.265,80.945z" fill-opacity=".5"/><path d="M0,0v85.982c20.003,17.3,40.005,29.506,80.011,29.506c51.791,0,74.688-48.569,151.751-48.569 c58.482,0,81.473,34.531,138.66,48.808c43.096-8.432,63.634-35.662,134.433-35.662c7.812,0,14.893,0.318,21.41,0.879 C567.086,45.555,597.381,0,665.691,0c61.856,0,85.369,27.782,110.246,51.947c18.295-10.536,40.891-18.276,73.378-18.276 c38.685,0,64.132,12.564,85.489,28.348c9.309-4.998,19.423-8.749,34.69-8.749c55.846,0,61.99,39.617,115.602,39.617 c58.08,0,57.521-46.595,115.015-46.595c39.804,0,59.986,22.479,79.888,39.69V0H0z"/>',
		);

		/**
		 * Filters the section divider paths.
		 *
		 * @param array $dividers Array list of available dividers.
		 */
		$this->dividers = apply_filters( 'et_section_dividers', $section_dividers );

		if ( null === self::$data_utils ) {
			self::$data_utils = ET_Core_Data_Utils::instance();
		}
	}

	/**
	 * Retrieves fields for divider settings.
	 *
	 * @since 3.23 Add responsive settings on Divider Style. Add allowed units on some range fields.
	 *
	 * @param  array $args Associative array for settings.
	 * @return array       Option settings.
	 */
	public function get_fields( array $args = array() ) {
		// Create an array so we don't get an error.
		$additional_options = array();

		// Create the options by first creating the structure.
		$structure = array();
		foreach ( array( 'top', 'bottom' ) as $placement ) :
			$structure[ "{$placement}_divider" ] = array(
				'controls' => array(
					"{$placement}_divider_style"       => array(
						'label'          => esc_html__( 'Divider Style', 'et_builder' ),
						'description'    => esc_html__( 'Select the divider shape that you would like to use. Shapes are represented visually within the list.', 'et_builder' ),
						'type'           => 'divider',
						'options'        => array(
							'none'        => et_builder_i18n( 'None' ),
							'slant'       => esc_html__( 'Slant', 'et_builder' ),
							'slant2'      => esc_html__( 'Slant 2', 'et_builder' ),
							'arrow'       => esc_html__( 'Arrow', 'et_builder' ),
							'arrow2'      => esc_html__( 'Arrow 2', 'et_builder' ),
							'arrow3'      => esc_html__( 'Arrow 3', 'et_builder' ),
							'ramp'        => esc_html__( 'Ramp', 'et_builder' ),
							'ramp2'       => esc_html__( 'Ramp 2', 'et_builder' ),
							'curve'       => esc_html__( 'Curve', 'et_builder' ),
							'curve2'      => esc_html__( 'Curve 2', 'et_builder' ),
							'mountains'   => esc_html__( 'Mountains', 'et_builder' ),
							'mountains2'  => esc_html__( 'Mountains 2', 'et_builder' ),
							'wave'        => esc_html__( 'Wave', 'et_builder' ),
							'wave2'       => esc_html__( 'Wave 2', 'et_builder' ),
							'waves'       => esc_html__( 'Waves', 'et_builder' ),
							'waves2'      => esc_html__( 'Waves 2', 'et_builder' ),
							'asymmetric'  => esc_html__( 'Asymmetric', 'et_builder' ),
							'asymmetric2' => esc_html__( 'Asymmetric 2', 'et_builder' ),
							'asymmetric3' => esc_html__( 'Asymmetric 3', 'et_builder' ),
							'asymmetric4' => esc_html__( 'Asymmetric 4', 'et_builder' ),
							'graph'       => esc_html__( 'Graph', 'et_builder' ),
							'graph2'      => esc_html__( 'Graph 2', 'et_builder' ),
							'graph3'      => esc_html__( 'Graph 3', 'et_builder' ),
							'graph4'      => esc_html__( 'Graph 4', 'et_builder' ),
							'triangle'    => esc_html__( 'Triangle', 'et_builder' ),
							'clouds'      => esc_html__( 'Clouds', 'et_builder' ),
							'clouds2'     => esc_html__( 'Clouds 2', 'et_builder' ),
						),
						'default'        => 'none',
						'flip'           => '',
						'mobile_options' => true,
					),
					"{$placement}_divider_color"       => array(
						'label'          => esc_html__( 'Divider Color', 'et_builder' ),
						'description'    => esc_html__( 'Pick a color to use for the section divider. By default, it will assume the color of the section above or below this section to ensure a smooth transition.', 'et_builder' ),
						'type'           => 'color-alpha',
						'default'        => '',
						'show_if_not'    => array(
							"{$placement}_divider_style" => 'none',
						),
						'mobile_options' => true,
					),
					"{$placement}_divider_height"      => array(
						'label'          => esc_html__( 'Divider Height', 'et_builder' ),
						'description'    => esc_html__( 'Increase or decrease the height of the shape divider.', 'et_builder' ),
						'type'           => 'range',
						'range_settings' => array(
							'min'  => 0,
							'max'  => 500,
							'step' => 1,
						),
						'default'        => '100px',
						'hover'          => 'tabs',
						'allowed_units'  => array( '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ),
						'default_unit'   => 'px',
						'show_if_not'    => array(
							"{$placement}_divider_style" => 'none',
						),
						'mobile_options' => true,
						'sticky'         => true,
					),
					"{$placement}_divider_repeat"      => array(
						'label'          => esc_html__( 'Divider Horizontal Repeat', 'et_builder' ),
						'description'    => esc_html__( 'Choose how many times the shape divider should repeat. Setting to 1x will remove all repetition.', 'et_builder' ),
						'type'           => 'range',
						'range_settings' => array(
							'min'       => 1,
							'max'       => 20,
							'step'      => 1,
							'min_limit' => 1, // Changed to 1 from 0 since it's basically the same result for both values.
						),
						'default'        => '1', // Dont use the fixed_unit in default value ( i.e. 1x, just use 1 ), or else input will return undefined.
						'fixed_unit'     => 'x',
						'show_if_not'    => array(
							"{$placement}_divider_style" => array( 'none', 'clouds', 'clouds2', 'triangle' ),
						),
						'mobile_options' => true,
					),
					"{$placement}_divider_flip"        => array(
						'label'           => esc_html__( 'Divider Flip', 'et_builder' ),
						'description'     => esc_html__( 'Flip the divider horizontally or vertically to change the shape and its direction.', 'et_builder' ),
						'type'            => 'multiple_buttons',
						'options'         => array(
							'horizontal' => array(
								'title' => esc_html__( 'Horizontal', 'et_builder' ),
								'icon'  => 'flip-horizontally',
							),
							'vertical'   => array(
								'title' => esc_html__( 'Vertical', 'et_builder' ),
								'icon'  => 'flip-vertically',
							),
						),
						'toggleable'      => true,
						'multi_selection' => true,
						'default'         => '',
						'show_if_not'     => array(
							"{$placement}_divider_style" => 'none',
						),
						'mobile_options'  => true,
					),
					"{$placement}_divider_arrangement" => array(
						'label'          => esc_html__( 'Divider Arrangement', 'et_builder' ),
						'description'    => esc_html__( 'Dividers can be placed either above or below section content. If placed above section content, then modules and rows within the section will be hidden behind the divider when they overlap.', 'et_builder' ),
						'type'           => 'select',
						'options'        => array(
							'above_content' => esc_html__( 'On Top Of Section Content', 'et_builder' ),
							'below_content' => esc_html__( 'Underneath Section Content', 'et_builder' ),
						),
						'default'        => 'below_content',
						'show_if_not'    => array(
							"{$placement}_divider_style" => 'none',
							'fullwidth'                  => 'on',
						),
						'mobile_options' => true,
					),
				),
			);

			// Automatically append responsive field
			foreach ( $structure[ "{$placement}_divider" ]['controls'] as $field_name => $field ) {
				if ( isset( $field['mobile_options'] ) && $field['mobile_options'] ) {
					$responsive_field_default = isset( $field['default'] ) ? $field['default'] : '';

					// Tablet field
					$structure[ "{$placement}_divider" ]['controls'][ "{$field_name}_tablet" ] = array(
						'type'    => 'hidden',
						'default' => $responsive_field_default,
					);

					// Phone field
					$structure[ "{$placement}_divider" ]['controls'][ "{$field_name}_phone" ] = array(
						'type'    => 'hidden',
						'default' => $responsive_field_default,
					);

					// Last edited field
					$structure[ "{$placement}_divider" ]['controls'][ "{$field_name}_last_edited" ] = array(
						'type'    => 'hidden',
						'default' => 'off|desktop',
					);
				}
			}
		endforeach; // End foreach().

		// Set our labels.
		$structure['bottom_divider']['label'] = et_builder_i18n( 'Bottom' );
		$structure['top_divider']['label']    = et_builder_i18n( 'Top' );

		$additional_options['divider_settings'] = array(
			'label'               => esc_html__( 'Dividers', 'et_builder' ),
			'description'         => esc_html__( 'Section dividers allow you to add creative shape transitions between different sections on your page.', 'et_builder' ),
			'tab_slug'            => $args['tab_slug'],
			'toggle_slug'         => $args['toggle_slug'],
			'attr_suffix'         => '',
			'type'                => 'composite',
			'option_category'     => 'layout',
			'composite_type'      => 'default',
			'composite_structure' => $structure,
		);

		return $additional_options;
	}

	/**
	 * Process Section Divider
	 *
	 * Adds a CSS class to the section, determines orientaion of the SVG, encodes an SVG to use as data
	 * for the background-image property.
	 *
	 * @since 3.23 Pass values parameter to support responsive settings.
	 * @since 4.6.0 Add sticky style support.
	 *
	 * @param  string $placement Whether it is the top or bottom divider.
	 * @param  array  $atts      Associative array of shortcode and their
	 *                           respective values.
	 * @param  string $breakpoint ''|tablet|phone
	 * @param  array  $values     Existing responsive values.
	 */
	public function process_svg( $placement, $atts, $breakpoint = '', $values = array() ) {
		// add a class to the section.
		$this->classes[] = sprintf( 'et_pb_%s_divider', esc_attr( $placement ) );

		// set some defaults.
		$previous_section = ! empty( $atts['prev_background_color'] ) ? $atts['prev_background_color'] : '#ffffff';
		$next_section     = ! empty( $atts['next_background_color'] ) ? $atts['next_background_color'] : '#ffffff';

		// set a default based on whether it is the top or bottom divider.
		$default_color = ( 'top' === $placement ) ? $previous_section : $next_section;
		$color         = ! empty( $atts[ "{$placement}_divider_color" ] ) ? $atts[ "{$placement}_divider_color" ] : $default_color;
		$height        = ! empty( $atts[ "{$placement}_divider_height" ] ) ? $atts[ "{$placement}_divider_height" ] : '100px';
		$height_hover  = et_pb_hover_options()->get_value( "{$placement}_divider_height", $atts, false );
		$repeat        = ! empty( $atts[ "{$placement}_divider_repeat" ] ) ? floatval( $atts[ "{$placement}_divider_repeat" ] ) : 1;
		$flip          = ( '' !== $atts[ "{$placement}_divider_flip" ] ) ? explode( '|', $atts[ "{$placement}_divider_flip" ] ) : array();
		$arrangement   = ! empty( $atts[ "{$placement}_divider_arrangement" ] ) ? $atts[ "{$placement}_divider_arrangement" ] : 'below_content';
		$divider_style = et_pb_responsive_options()->get_any_value( $atts, "{$placement}_divider_style", '', true, $breakpoint );
		$style         = $divider_style . "-{$placement}";
		$fullwidth     = $atts['fullwidth'];

		// Apply adjustment for responsive styling
		if ( '' !== $breakpoint ) {
			// Get all responsive unique value.
			$values = et_pb_responsive_options()->get_any_responsive_values(
				$atts,
				array(
					"{$placement}_divider_color"       => '',
					"{$placement}_divider_height"      => '',
					"{$placement}_divider_repeat"      => '',
					"{$placement}_divider_flip"        => '',
					"{$placement}_divider_arrangement" => '',
				),
				true,
				$breakpoint
			);

			// Replace all default values.
			$color       = ! empty( $values[ "{$placement}_divider_color" ] ) ? $values[ "{$placement}_divider_color" ] : $color;
			$height      = ! empty( $values[ "{$placement}_divider_height" ] ) ? $values[ "{$placement}_divider_height" ] : $height;
			$repeat      = ! empty( $values[ "{$placement}_divider_repeat" ] ) ? floatval( $values[ "{$placement}_divider_repeat" ] ) : $repeat;
			$flip        = ! empty( $values[ "{$placement}_divider_flip" ] ) ? explode( '|', $values[ "{$placement}_divider_flip" ] ) : $flip;
			$arrangement = ! empty( $values[ "{$placement}_divider_arrangement" ] ) ? $values[ "{$placement}_divider_arrangement" ] : $arrangement;

			if ( ! empty( $values[ "{$placement}_divider_flip" ] ) && 'none' === $values[ "{$placement}_divider_flip" ] ) {
				$flip = array();
			}
		}

		// Make sure that we don't divide by zero.
		if ( ! $repeat ) {
			$repeat = 1;
		}

		// let's make sure we flip the fight ones, yeah?
		// use the opposite SVG
		if ( in_array( 'vertical', $flip ) ) {
			switch ( $placement ) {
				case 'top':
					$style = $divider_style . '-bottom';
					break;
				case 'bottom':
					$style = $divider_style . '-top';
					break;
			}
		}

		// The SVG markup for the background.
		switch ( $style ) {
			case 'clouds-top':
			case 'clouds2-top':
				// we can use the viewBox to move down the image since it has a height of 86px
				$svg_markup = '<svg width="100%%" height="%1$s" viewBox="0 0 1280 86" preserveAspectRatio="xMidYMid slice" xmlns="http://www.w3.org/2000/svg"><g fill="%2$s">%3$s</g></svg>';
				break;
			case 'clouds-bottom':
			case 'clouds2-bottom':
				// we can use the viewBox to move up the image since it has a height of 86px
				$svg_markup = '<svg width="100%%" viewBox="0 0 1280 86" preserveAspectRatio="xMidYMid slice" xmlns="http://www.w3.org/2000/svg"><g fill="%2$s">%3$s</g></svg>';
				break;
			case 'triangle-top':
				$svg_markup = '<svg width="100%%" height="100px" viewBox="0 0 1280 100" preserveAspectRatio="xMidYMid slice" xmlns="http://www.w3.org/2000/svg"><g fill="%2$s">%3$s</g></svg>';
				break;
			case 'triangle-bottom':
				$svg_markup = '<svg width="160px" height="140px" viewBox="0 0 160 140" xmlns="http://www.w3.org/2000/svg"><g fill="%2$s">%3$s</g></svg>';
				break;
			default:
				$svg_markup = '<svg width="100%%" height="%1$s" viewBox="0 0 1280 140" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg"><g fill="%2$s">%3$s</g></svg>';
				break;
		}

		$divider_style = isset( $this->dividers[ $style ] ) ? $this->dividers[ $style ] : '';

		$svg = sprintf( $svg_markup, $height, $color, $divider_style );

		// encode the SVG so we can use it as data for background-image.
		$this->svg = base64_encode( $svg ); // phpcs:ignore

		// Build up our declaration.
		// bg-image
		$declaration['background-image'] = sprintf( 'url( data:image/svg+xml;base64,%s )', $this->svg );

		// bg-size. the percent is how many times to repeat the image.
		if ( 0 === strpos( $style, 'clouds' ) ) {
			$declaration['background-size'] = 'cover';
			switch ( $placement ) {
				case 'top':
					$declaration['background-position'] = ( 'top' === $placement || 'vertical' === $flip ) ? 'center top' : 'center bottom';
					break;
				case 'bottom':
					$declaration['background-position'] = ( 'top' === $placement || 'vertical' !== $flip ) ? 'center top' : 'center bottom';
					break;
			}
		} elseif ( 0 === strpos( $style, 'triangle' ) ) {
			$declaration['background-size']       = 'cover';
			$declaration['background-position-x'] = 'center';
		} else {

			// Adjusts for when percentages are being used.
			if ( 0 < strpos( $height, '%' ) ) {
				$declaration['background-size'] = sprintf( '%1$s%% 100%%', floatval( 100 / $repeat ) );
			} else {
				$declaration['background-size'] = sprintf( '%1$s%% %2$s', floatval( 100 / $repeat ), $height );
			}
		}

		// position
		$declaration[ $placement ] = 0;

		// height
		$declaration['height'] = $height;

		// z-index - determined by arrangement.
		$declaration['z-index'] = ( 'on' === $fullwidth || 'above_content' === $arrangement ) ? 10 : 1;

		$flip_styles = array(
			in_array( 'horizontal', $flip, true ) ? '-1' : '1',
			in_array( 'vertical', $flip, true ) ? '-1' : '1',
		);

		$declaration['transform'] = 'scale(' . implode( ', ', $flip_styles ) . ')';

		// finally create our CSS declaration.
		$css = '';
		foreach ( $declaration as $rule => $value ) {
			$css .= esc_html( "{$rule}:{$value};" );
		}

		// prepare our selector.
		$selector = sprintf( '%%order_class%%.section_has_divider.et_pb_%1$s_divider .et_pb_%1$s_inside_divider', esc_attr( $placement ) );

		// The styling of our section divider.
		$styling = array(
			'selector'    => $selector,
			'declaration' => $css,
		);

		// Apply media query if needed
		if ( in_array( $breakpoint, array( 'tablet', 'phone' ) ) ) {
			$query_map = array(
				'tablet' => 'max_width_980',
				'phone'  => 'max_width_767',
			);

			$styling['media_query'] = ET_Builder_Element::get_media_query( $query_map[ $breakpoint ] );
		}

		ET_Builder_Element::set_style( 'et_pb_section', $styling );

		// if we are on the first section and is the top divider.
		if ( 0 === $this->count && 'top' === $placement && '' === $breakpoint ) {
			// we will use a transparent bg.
			ET_Builder_Element::set_style(
				'et_pb_section',
				array(
					'selector'    => $selector,
					'declaration' => 'background-color: transparent;',
				)
			);
		}

		// Print Hover / Sticky height.
		$modes = array( 'hover', 'sticky' );

		// sprintf() removes `%` while add_hover* and add_sticky* only recognize %%order_class%%.
		// thus append mode selector before $placement is re-added to the selector.
		$height_selector_base = '%%order_class%%.section_has_divider.et_pb_%1$s_divider .et_pb_%1$s_inside_divider';

		foreach ( $modes as $mode ) {
			switch ( $mode ) {
				case 'hover':
					$helper               = et_pb_hover_options();
					$height_mode_selector = $helper->add_hover_to_order_class( $height_selector_base );
					break;

				case 'sticky':
					$helper               = et_pb_sticky_options();
					$height_mode_selector = $helper->add_sticky_to_order_class( $height_selector_base, $helper->is_sticky_module( $atts ) );
					break;
			}

			$height_mode = $helper->get_value( "{$placement}_divider_height", $atts, false );

			if ( false === $height_mode ) {
				continue;
			}

			$css      = '';
			$height   = $height_mode;
			$selector = sprintf(
				$height_mode_selector,
				esc_attr( $placement )
			);

			$declaration = array(
				'height' => $height,
			);

			// Adjusts for when percentages are being used.
			if ( 0 < strpos( $height, '%' ) ) {
				$declaration['background-size'] = sprintf( '%1$s%% 100%%', floatval( 100 / $repeat ) );
			} else {
				$declaration['background-size'] = sprintf( '%1$s%% %2$s', floatval( 100 / $repeat ), $height );
			}

			foreach ( $declaration as $rule => $value ) {
				$css .= esc_html( "{$rule}:{$value};" );
			}

			ET_Builder_Element::set_style(
				'et_pb_section',
				array(
					'selector'    => "$selector",
					'declaration' => $css,
				)
			);
		}
	}

	/**
	 * Returns a placeholder for the section only if it is set to be inside of the section.
	 *
	 * @param  string $placement Whether it is the top or bottom
	 * @return string            HTML container
	 */
	public function get_svg( $placement ) {
		// we return a div to use for the divider
		return sprintf( '<div class="et_pb_%s_inside_divider et-no-transition"></div>', esc_attr( $placement ) );
	}
}

return new ET_Builder_Module_Field_Divider();
