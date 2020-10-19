<?php

namespace App\Support;

use App\Contracts\ImageMetadata;

class Iptc implements ImageMetadata {

	public $iptcTagCodes = [
		// IPTC.Envelope
		"1#000" => 'ModelVersion',
		"1#005" => 'Destination',
		"1#020" => 'FileFormat',
		"1#022" => 'FileVersion',
		"1#030" => 'ServiceId',
		"1#040" => 'EnvelopeNumber',
		"1#050" => 'ProductId',
		"1#060" => 'EnvelopePriority',
		"1#070" => 'DateSent',
		"1#080" => 'TimeSent',
		"1#090" => 'CharacterSet',
		"1#100" => 'UNO',
		"1#120" => 'ARMId',
		"1#122" => 'ARMVersion',
		// IPTC.Application2
		"2#000" => 'RecordVersion',
		"2#003" => 'ObjectType',
		"2#004" => 'ObjectAttribute',
		"2#005" => 'ObjectName',
		"2#007" => 'EditStatus',
		"2#008" => 'EditorialUpdate',
		"2#010" => 'Urgency',
		"2#012" => 'Subject',
		"2#015" => 'Category',
		"2#020" => 'SuppCategory',
		"2#022" => 'FixtureId',
		"2#025" => 'Keywords',
		"2#026" => 'LocationCode',
		"2#027" => 'LocationName',
		"2#030" => 'ReleaseDate',
		"2#035" => 'ReleaseTime',
		"2#037" => 'ExpirationDate',
		"2#038" => 'ExpirationTime',
		"2#040" => 'SpecialInstructions',
		"2#042" => 'ActionAdvised',
		"2#045" => 'ReferenceService',
		"2#047" => 'ReferenceDate',
		"2#050" => 'ReferenceNumber',
		"2#055" => 'DateCreated',
		"2#060" => 'TimeCreated',
		"2#062" => 'DigitizationDate',
		"2#063" => 'DigitizationTime',
		"2#065" => 'Program',
		"2#070" => 'ProgramVersion',
		"2#075" => 'ObjectCycle',
		"2#080" => 'Byline',
		"2#085" => 'BylineTitle',
		"2#090" => 'City',
		"2#092" => 'SubLocation',
		"2#095" => 'ProvinceState',
		"2#100" => 'CountryCode',
		"2#101" => 'CountryName',
		"2#103" => 'TransmissionReference',
		"2#105" => 'Headline',
		"2#110" => 'Credit',
		"2#115" => 'Source',
		"2#116" => 'Copyright',
		"2#118" => 'Contact',
		"2#120" => 'Caption',
		"2#122" => 'Writer',
		"2#125" => 'RasterizedCaption',
		"2#130" => 'ImageType',
		"2#131" => 'ImageOrientation',
		"2#135" => 'Language',
		"2#150" => 'AudioType',
		"2#151" => 'AudioRate',
		"2#152" => 'AudioResolution',
		"2#153" => 'AudioDuration',
		"2#154" => 'AudioOutcue',
		"2#200" => 'PreviewFormat',
		"2#201" => 'PreviewVersion',
		"2#202" => 'Preview',
	];

	public function convertIptcTagCodes($iptc): array {
		$result = [];
		if (is_array($iptc)) {
			foreach ($iptc as $code => $tags)
				$result[$this->iptcTagCodes[$code] ?? $code] = $tags;
		}
		return $result;
	}

	public function embed(string $file, array $metadata): string {
		$data = '';
		foreach ($metadata as $tag => $values) {
			foreach ($values as $value)
				$data .= $this->makeIptcTag($tag, $value);
		}
dd($data);
		return iptcembed($data, $file);
	}

	public function getData(string $file, array $tags = null): array {
		getimagesize($file, $info);
		$data = isset($info['APP13']) ? iptcparse($info['APP13']) : [];
		return $tags ? array_intersect_key($data, array_flip($tags)) : $data;
	}

	/**
	 * iptc_make_tag() function by Thies C. Arntzen
	 */
	function makeIptcTag($tag, $value) {
		list($part, $code) = explode('#', $tag);
		$len = strlen($value);
		$retval = chr(0x1C) . chr($part) . chr($code);

		if($len < 0x8000) {
			$retval .= chr($len >> 8) .
				chr($len & 0xFF);
		} else {
			$retval .= chr(0x80) .
				chr(0x04) .
				chr(($len >> 24) & 0xFF) .
				chr(($len >> 16) & 0xFF) .
				chr(($len >> 8) & 0xFF) .
				chr($len & 0xFF);
		}

		return $retval . $value;
	}

}
