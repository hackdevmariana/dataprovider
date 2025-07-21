<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $type
 * @property bool $is_primary
 * @property int $person_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Person $person
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alias newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alias newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alias query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alias whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alias whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alias whereIsPrimary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alias whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alias wherePersonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alias whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alias whereUpdatedAt($value)
 */
	class Alias extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $day
 * @property int $month
 * @property string|null $year
 * @property string $title
 * @property string $slug
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $tags
 * @property-read int|null $tags_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anniversary newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anniversary newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anniversary query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anniversary whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anniversary whereDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anniversary whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anniversary whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anniversary whereMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anniversary whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anniversary whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anniversary whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anniversary whereYear($value)
 */
	class Anniversary extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property string $token
 * @property string $scope
 * @property int $rate_limit
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property bool $is_revoked
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey whereIsRevoked($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey whereRateLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey whereScope($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey whereUserId($value)
 */
	class ApiKey extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $organization_id
 * @property string $name
 * @property string|null $slogan
 * @property string|null $primary_color
 * @property string|null $secondary_color
 * @property string $locale
 * @property string|null $custom_js
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \App\Models\Organization $organization
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppSetting whereCustomJs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppSetting whereLocale($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppSetting whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppSetting whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppSetting wherePrimaryColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppSetting whereSecondaryColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppSetting whereSlogan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppSetting whereUpdatedAt($value)
 */
	class AppSetting extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $person_id
 * @property float|null $height_cm
 * @property float|null $weight_kg
 * @property string|null $body_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Person $person
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appearance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appearance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appearance query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appearance whereBodyType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appearance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appearance whereHeightCm($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appearance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appearance wherePersonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appearance whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appearance whereWeightKg($value)
 */
	class Appearance extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $birth_date
 * @property string|null $genre
 * @property int|null $person_id
 * @property string|null $stage_name
 * @property string|null $group_name
 * @property int|null $active_years_start
 * @property int|null $active_years_end
 * @property string|null $bio
 * @property string|null $photo
 * @property array<array-key, mixed>|null $social_links
 * @property int|null $language_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Event> $events
 * @property-read int|null $events_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Group> $groups
 * @property-read int|null $groups_count
 * @property-read \App\Models\Language|null $language
 * @property-read \App\Models\Person|null $person
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist whereActiveYearsEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist whereActiveYearsStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist whereBio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist whereBirthDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist whereGenre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist whereGroupName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist wherePersonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist whereSocialLinks($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist whereStageName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist whereUpdatedAt($value)
 */
	class Artist extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $code
 * @property int $country_id
 * @property string|null $latitude
 * @property string|null $longitude
 * @property string|null $area_km2
 * @property int|null $altitude_m
 * @property int|null $timezone_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Country $country
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Province> $provinces
 * @property-read int|null $provinces_count
 * @property-read \App\Models\Timezone|null $timezone
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutonomousCommunity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutonomousCommunity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutonomousCommunity query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutonomousCommunity whereAltitudeM($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutonomousCommunity whereAreaKm2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutonomousCommunity whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutonomousCommunity whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutonomousCommunity whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutonomousCommunity whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutonomousCommunity whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutonomousCommunity whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutonomousCommunity whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutonomousCommunity whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutonomousCommunity whereTimezoneId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutonomousCommunity whereUpdatedAt($value)
 */
	class AutonomousCommunity extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $awarded_by
 * @property string|null $first_year_awarded
 * @property string|null $category
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AwardWinner> $awardWinners
 * @property-read int|null $award_winners_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Award newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Award newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Award query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Award whereAwardedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Award whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Award whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Award whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Award whereFirstYearAwarded($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Award whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Award whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Award whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Award whereUpdatedAt($value)
 */
	class Award extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $person_id
 * @property int $award_id
 * @property int|null $year
 * @property string $classification
 * @property int|null $work_id
 * @property int|null $municipality_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Award $award
 * @property-read \App\Models\Municipality|null $municipality
 * @property-read \App\Models\Person $person
 * @property-read \App\Models\Work|null $work
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AwardWinner newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AwardWinner newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AwardWinner query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AwardWinner whereAwardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AwardWinner whereClassification($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AwardWinner whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AwardWinner whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AwardWinner whereMunicipalityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AwardWinner wherePersonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AwardWinner whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AwardWinner whereWorkId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AwardWinner whereYear($value)
 */
	class AwardWinner extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $date
 * @property string $slug
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CalendarHolidayLocation> $locations
 * @property-read int|null $locations_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CalendarHoliday newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CalendarHoliday newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CalendarHoliday query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CalendarHoliday whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CalendarHoliday whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CalendarHoliday whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CalendarHoliday whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CalendarHoliday whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CalendarHoliday whereUpdatedAt($value)
 */
	class CalendarHoliday extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $calendar_holiday_id
 * @property int|null $municipality_id
 * @property int|null $province_id
 * @property int|null $autonomous_community_id
 * @property int|null $country_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AutonomousCommunity|null $autonomousCommunity
 * @property-read \App\Models\Country|null $country
 * @property-read \App\Models\CalendarHoliday $holiday
 * @property-read \App\Models\Municipality|null $municipality
 * @property-read \App\Models\Province|null $province
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CalendarHolidayLocation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CalendarHolidayLocation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CalendarHolidayLocation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CalendarHolidayLocation whereAutonomousCommunityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CalendarHolidayLocation whereCalendarHolidayId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CalendarHolidayLocation whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CalendarHolidayLocation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CalendarHolidayLocation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CalendarHolidayLocation whereMunicipalityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CalendarHolidayLocation whereProvinceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CalendarHolidayLocation whereUpdatedAt($value)
 */
	class CalendarHolidayLocation extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $description
 * @property string $kg_co2e
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CarbonSavingLog> $savingLogs
 * @property-read int|null $saving_logs_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonEquivalence newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonEquivalence newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonEquivalence query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonEquivalence whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonEquivalence whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonEquivalence whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonEquivalence whereKgCo2e($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonEquivalence whereUpdatedAt($value)
 */
	class CarbonEquivalence extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $carbon_equivalence_id
 * @property string $amount_kg
 * @property string|null $activity_type
 * @property string|null $metadata
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Cooperative|null $cooperative
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CarbonEquivalence> $equivalences
 * @property-read int|null $equivalences_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingLog whereActivityType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingLog whereAmountKg($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingLog whereCarbonEquivalenceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingLog whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingLog whereUserId($value)
 */
	class CarbonSavingLog extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $hex_code
 * @property string|null $rgb_code
 * @property string|null $hsl_code
 * @property int $is_primary
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Color> $colorables
 * @property-read int|null $colorables_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Color newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Color newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Color query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Color whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Color whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Color whereHexCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Color whereHslCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Color whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Color whereIsPrimary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Color whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Color whereRgbCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Color whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Color whereUpdatedAt($value)
 */
	class Color extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $icon_url
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyType whereIconUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyType whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyType whereUpdatedAt($value)
 */
	class CompanyType extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $legal_name
 * @property string $cooperative_type
 * @property string $scope
 * @property string|null $nif
 * @property \Illuminate\Support\Carbon|null $founded_at
 * @property string $phone
 * @property string $email
 * @property string $website
 * @property string|null $logo_url
 * @property int|null $image_id
 * @property int $municipality_id
 * @property string $address
 * @property float|null $latitude
 * @property float|null $longitude
 * @property string|null $description
 * @property int|null $number_of_members
 * @property string $main_activity
 * @property bool $is_open_to_new_members
 * @property string $source
 * @property int|null $data_source_id
 * @property bool $has_energy_market_access
 * @property string|null $legal_form
 * @property string|null $statutes_url
 * @property bool $accepts_new_installations
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\DataSource|null $dataSource
 * @property-read \App\Models\Image|null $image
 * @property-read \App\Models\Municipality $municipality
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CooperativeUserMember> $userMemberships
 * @property-read int|null $user_memberships_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereAcceptsNewInstallations($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereCooperativeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereDataSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereFoundedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereHasEnergyMarketAccess($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereImageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereIsOpenToNewMembers($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereLegalForm($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereLegalName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereLogoUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereMainActivity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereMunicipalityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereNif($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereNumberOfMembers($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereScope($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereStatutesUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereWebsite($value)
 */
	class Cooperative extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $cooperative_id
 * @property int $user_id
 * @property string|null $role
 * @property \Illuminate\Support\Carbon|null $joined_at
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Cooperative $cooperative
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperativeUserMember newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperativeUserMember newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperativeUserMember query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperativeUserMember whereCooperativeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperativeUserMember whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperativeUserMember whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperativeUserMember whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperativeUserMember whereJoinedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperativeUserMember whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperativeUserMember whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperativeUserMember whereUserId($value)
 */
	class CooperativeUserMember extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $iso_alpha2
 * @property string $iso_alpha3
 * @property string|null $iso_numeric
 * @property string|null $demonym
 * @property string|null $official_language
 * @property string|null $currency_code
 * @property string|null $phone_code
 * @property string|null $latitude
 * @property string|null $longitude
 * @property string|null $flag_url
 * @property int|null $population
 * @property string|null $gdp_usd
 * @property string|null $region_group
 * @property string|null $area_km2
 * @property int|null $altitude_m
 * @property int|null $timezone_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Language> $languages
 * @property-read int|null $languages_count
 * @property-read \App\Models\Timezone|null $timezone
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereAltitudeM($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereAreaKm2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereCurrencyCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereDemonym($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereFlagUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereGdpUsd($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereIsoAlpha2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereIsoAlpha3($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereIsoNumeric($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereOfficialLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country wherePhoneCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country wherePopulation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereRegionGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereTimezoneId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereUpdatedAt($value)
 */
	class Country extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property string $iso_code
 * @property string $symbol
 * @property string $name
 * @property int $is_crypto
 * @property int $is_supported_by_app
 * @property int $exchangeable_in_calculator
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency whereExchangeableInCalculator($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency whereIsCrypto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency whereIsSupportedByApp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency whereIsoCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency whereSymbol($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency whereUpdatedAt($value)
 */
	class Currency extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $type
 * @property string|null $url
 * @property string|null $license
 * @property string|null $last_scraped_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Stat> $stats
 * @property-read int|null $stats_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataSource newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataSource newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataSource query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataSource whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataSource whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataSource whereLastScrapedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataSource whereLicense($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataSource whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataSource whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataSource whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataSource whereUrl($value)
 */
	class DataSource extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $energy_company_id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $price_fixed_eur_month
 * @property string|null $price_variable_eur_kwh
 * @property int|null $price_unit_id
 * @property string $offer_type
 * @property \Illuminate\Support\Carbon|null $valid_from
 * @property \Illuminate\Support\Carbon|null $valid_until
 * @property string|null $conditions_url
 * @property int|null $contract_length_months
 * @property bool $requires_smart_meter
 * @property bool $renewable_origin_certified
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\EnergyCompany $energyCompany
 * @property-read \App\Models\PriceUnit|null $priceUnit
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityOffer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityOffer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityOffer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityOffer whereConditionsUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityOffer whereContractLengthMonths($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityOffer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityOffer whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityOffer whereEnergyCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityOffer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityOffer whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityOffer whereOfferType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityOffer wherePriceFixedEurMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityOffer wherePriceUnitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityOffer wherePriceVariableEurKwh($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityOffer whereRenewableOriginCertified($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityOffer whereRequiresSmartMeter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityOffer whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityOffer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityOffer whereValidFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityOffer whereValidUntil($value)
 */
	class ElectricityOffer extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon $date
 * @property int|null $hour
 * @property string $type
 * @property string $price_eur_mwh
 * @property string|null $price_min
 * @property string|null $price_max
 * @property string|null $price_avg
 * @property bool $forecast_for_tomorrow
 * @property string|null $source
 * @property int|null $price_unit_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ElectricityPriceInterval> $intervals
 * @property-read int|null $intervals_count
 * @property-read \App\Models\PriceUnit|null $priceUnit
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityPrice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityPrice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityPrice query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityPrice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityPrice whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityPrice whereForecastForTomorrow($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityPrice whereHour($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityPrice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityPrice wherePriceAvg($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityPrice wherePriceEurMwh($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityPrice wherePriceMax($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityPrice wherePriceMin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityPrice wherePriceUnitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityPrice whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityPrice whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityPrice whereUpdatedAt($value)
 */
	class ElectricityPrice extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $electricity_price_id
 * @property int $interval_index
 * @property string $start_time
 * @property string $end_time
 * @property string $price_eur_mwh
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ElectricityPrice $electricityPrice
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityPriceInterval newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityPriceInterval newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityPriceInterval query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityPriceInterval whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityPriceInterval whereElectricityPriceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityPriceInterval whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityPriceInterval whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityPriceInterval whereIntervalIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityPriceInterval wherePriceEurMwh($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityPriceInterval whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityPriceInterval whereUpdatedAt($value)
 */
	class ElectricityPriceInterval extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $activity
 * @property string $factor_kg_co2e_per_unit
 * @property string $unit
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Region|null $region
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmissionFactor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmissionFactor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmissionFactor query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmissionFactor whereActivity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmissionFactor whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmissionFactor whereFactorKgCo2ePerUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmissionFactor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmissionFactor whereUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmissionFactor whereUpdatedAt($value)
 */
	class EmissionFactor extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property string $building_type
 * @property string $energy_rating
 * @property string $annual_energy_consumption_kwh
 * @property string $annual_emissions_kg_co2e
 * @property int|null $zone_climate_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Cooperative|null $cooperative
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCertificate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCertificate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCertificate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCertificate whereAnnualEmissionsKgCo2e($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCertificate whereAnnualEnergyConsumptionKwh($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCertificate whereBuildingType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCertificate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCertificate whereEnergyRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCertificate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCertificate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCertificate whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCertificate whereZoneClimateId($value)
 */
	class EnergyCertificate extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $website
 * @property string|null $phone_customer
 * @property string|null $phone_commercial
 * @property string|null $email_customer
 * @property string|null $email_commercial
 * @property string|null $highlighted_offer
 * @property string|null $cnmc_id
 * @property string|null $logo_url
 * @property int|null $image_id
 * @property string $company_type
 * @property string|null $address
 * @property string|null $latitude
 * @property string|null $longitude
 * @property string $coverage_scope
 * @property int|null $municipality_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Image|null $image
 * @property-read \App\Models\Municipality|null $municipality
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCompany newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCompany newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCompany query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCompany whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCompany whereCnmcId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCompany whereCompanyType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCompany whereCoverageScope($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCompany whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCompany whereEmailCommercial($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCompany whereEmailCustomer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCompany whereHighlightedOffer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCompany whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCompany whereImageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCompany whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCompany whereLogoUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCompany whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCompany whereMunicipalityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCompany whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCompany wherePhoneCommercial($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCompany wherePhoneCustomer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCompany whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCompany whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCompany whereWebsite($value)
 */
	class EnergyCompany extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $type
 * @property float $capacity_kw
 * @property string $location
 * @property int|null $owner_id
 * @property \Illuminate\Support\Carbon|null $commissioned_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $owner
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyInstallation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyInstallation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyInstallation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyInstallation whereCapacityKw($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyInstallation whereCommissionedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyInstallation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyInstallation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyInstallation whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyInstallation whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyInstallation whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyInstallation whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyInstallation whereUpdatedAt($value)
 */
	class EnergyInstallation extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $producer_id
 * @property int $consumer_id
 * @property int $installation_id
 * @property float $amount_kwh
 * @property float $price_per_kwh
 * @property \Illuminate\Support\Carbon $transaction_datetime
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $consumer
 * @property-read \App\Models\EnergyInstallation $installation
 * @property-read \App\Models\User $producer
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyTransaction whereAmountKwh($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyTransaction whereConsumerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyTransaction whereInstallationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyTransaction wherePricePerKwh($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyTransaction whereProducerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyTransaction whereTransactionDatetime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyTransaction whereUpdatedAt($value)
 */
	class EnergyTransaction extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string|null $description
 * @property \Illuminate\Support\Carbon $start_datetime
 * @property \Illuminate\Support\Carbon|null $end_datetime
 * @property int|null $venue_id
 * @property int|null $event_type_id
 * @property int|null $festival_id
 * @property int|null $language_id
 * @property int|null $timezone_id
 * @property int|null $municipality_id
 * @property int|null $point_of_interest_id
 * @property int|null $work_id
 * @property numeric|null $price
 * @property bool $is_free
 * @property int|null $audience_size_estimate
 * @property string|null $source_url
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Artist> $artists
 * @property-read int|null $artists_count
 * @property-read \App\Models\EventType|null $eventType
 * @property-read \App\Models\Festival|null $festival
 * @property-read \App\Models\Language|null $language
 * @property-read \App\Models\Municipality|null $municipality
 * @property-read \App\Models\PointOfInterest|null $pointOfInterest
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $tags
 * @property-read int|null $tags_count
 * @property-read \App\Models\Timezone|null $timezone
 * @property-read \App\Models\Venue|null $venue
 * @property-read \App\Models\Work|null $work
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereAudienceSizeEstimate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereEndDatetime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereEventTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereFestivalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereIsFree($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereMunicipalityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event wherePointOfInterestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereSourceUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereStartDatetime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereTimezoneId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereVenueId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereWorkId($value)
 */
	class Event extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Event> $events
 * @property-read int|null $events_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventType whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventType whereUpdatedAt($value)
 */
	class EventType extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $from_currency
 * @property string $to_currency
 * @property string $rate
 * @property \Illuminate\Support\Carbon $date
 * @property string $source
 * @property string $market_type
 * @property int|null $precision
 * @property string|null $unit
 * @property string|null $volume_usd
 * @property string|null $market_cap
 * @property \Illuminate\Support\Carbon|null $retrieved_at
 * @property bool $is_active
 * @property bool $is_promoted
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExchangeRate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExchangeRate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExchangeRate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExchangeRate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExchangeRate whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExchangeRate whereFromCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExchangeRate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExchangeRate whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExchangeRate whereIsPromoted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExchangeRate whereMarketCap($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExchangeRate whereMarketType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExchangeRate wherePrecision($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExchangeRate whereRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExchangeRate whereRetrievedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExchangeRate whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExchangeRate whereToCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExchangeRate whereUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExchangeRate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExchangeRate whereVolumeUsd($value)
 */
	class ExchangeRate extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $person_id
 * @property int $relative_id
 * @property string $relationship_type
 * @property bool $is_biological
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Person $person
 * @property-read \App\Models\Person $relative
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FamilyMember newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FamilyMember newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FamilyMember query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FamilyMember whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FamilyMember whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FamilyMember whereIsBiological($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FamilyMember wherePersonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FamilyMember whereRelationshipType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FamilyMember whereRelativeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FamilyMember whereUpdatedAt($value)
 */
	class FamilyMember extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property int|null $month
 * @property string|null $usual_days
 * @property int $recurring
 * @property int $location_id
 * @property string|null $logo_url
 * @property string|null $color_theme
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Artist> $artists
 * @property-read int|null $artists_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Event> $events
 * @property-read int|null $events_count
 * @property-read \App\Models\Municipality $location
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $tags
 * @property-read int|null $tags_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Festival newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Festival newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Festival query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Festival whereColorTheme($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Festival whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Festival whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Festival whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Festival whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Festival whereLogoUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Festival whereMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Festival whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Festival whereRecurring($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Festival whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Festival whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Festival whereUsualDays($value)
 */
	class Festival extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string|null $family
 * @property string $style
 * @property int|null $weight
 * @property string|null $license
 * @property string $source_url
 * @property int $is_default
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Font> $fontables
 * @property-read int|null $fontables_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Font newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Font newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Font query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Font whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Font whereFamily($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Font whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Font whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Font whereLicense($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Font whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Font whereSourceUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Font whereStyle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Font whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Font whereWeight($value)
 */
	class Font extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Artist> $artists
 * @property-read int|null $artists_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Artist> $members
 * @property-read int|null $members_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereUpdatedAt($value)
 */
	class Group extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $url
 * @property string|null $alt_text
 * @property string|null $source
 * @property int|null $width
 * @property int|null $height
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $related
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereAltText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereHeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereWidth($value)
 */
	class Image extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $language
 * @property string $slug
 * @property string|null $native_name
 * @property string|null $iso_639_1
 * @property string|null $iso_639_2
 * @property int $rtl
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Country> $countries
 * @property-read int|null $countries_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language whereIso6391($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language whereIso6392($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language whereLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language whereNativeName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language whereRtl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language whereUpdatedAt($value)
 */
	class Language extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $url
 * @property string|null $label
 * @property string $related_type
 * @property int $related_id
 * @property string $type
 * @property bool $is_primary
 * @property bool $opens_in_new_tab
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $related
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Link newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Link newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Link query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Link whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Link whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Link whereIsPrimary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Link whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Link whereOpensInNewTab($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Link whereRelatedId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Link whereRelatedType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Link whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Link whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Link whereUrl($value)
 */
	class Link extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $media_outlet_id
 * @property string $type
 * @property string|null $contact_name
 * @property string|null $phone
 * @property string|null $email
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\MediaOutlet $mediaOutlet
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaContact newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaContact newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaContact query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaContact whereContactName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaContact whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaContact whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaContact whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaContact whereMediaOutletId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaContact wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaContact whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaContact whereUpdatedAt($value)
 */
	class MediaContact extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $type
 * @property string|null $website
 * @property string|null $headquarters_location
 * @property int|null $municipality_id
 * @property string|null $language
 * @property int|null $circulation
 * @property int|null $founding_year
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MediaContact> $contacts
 * @property-read int|null $contacts_count
 * @property-read \App\Models\Municipality|null $municipality
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\NewsArticle> $newsArticles
 * @property-read int|null $news_articles_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet whereCirculation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet whereFoundingYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet whereHeadquartersLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet whereLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet whereMunicipalityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet whereWebsite($value)
 */
	class MediaOutlet extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $ine_code
 * @property string|null $postal_code
 * @property int|null $population
 * @property string|null $mayor_name
 * @property string|null $mayor_salary
 * @property string|null $latitude
 * @property string|null $longitude
 * @property string|null $area_km2
 * @property int|null $altitude_m
 * @property int $is_capital
 * @property string|null $tourism_info
 * @property int|null $region_id
 * @property int $province_id
 * @property int $autonomous_community_id
 * @property int $country_id
 * @property int|null $timezone_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AutonomousCommunity $autonomousCommunity
 * @property-read \App\Models\Country $country
 * @property-read \App\Models\Province $province
 * @property-read \App\Models\Region|null $region
 * @property-read \App\Models\Timezone|null $timezone
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality whereAltitudeM($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality whereAreaKm2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality whereAutonomousCommunityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality whereIneCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality whereIsCapital($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality whereMayorName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality whereMayorSalary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality wherePopulation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality wherePostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality whereProvinceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality whereRegionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality whereTimezoneId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality whereTourismInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality whereUpdatedAt($value)
 */
	class Municipality extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string|null $summary
 * @property string|null $content
 * @property string $source_url
 * @property \Illuminate\Support\Carbon|null $published_at
 * @property \Illuminate\Support\Carbon|null $featured_start
 * @property \Illuminate\Support\Carbon|null $featured_end
 * @property int $media_outlet_id
 * @property int|null $author_id
 * @property int|null $municipality_id
 * @property int|null $language_id
 * @property int|null $image_id
 * @property int|null $tag_id
 * @property bool $is_outstanding
 * @property bool $is_verified
 * @property bool $is_scraped
 * @property bool $is_translated
 * @property string $visibility
 * @property int $views_count
 * @property array<array-key, mixed>|null $tags
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Person|null $author
 * @property-read \App\Models\Image|null $image
 * @property-read \App\Models\Language|null $language
 * @property-read \App\Models\MediaOutlet $mediaOutlet
 * @property-read \App\Models\Municipality|null $municipality
 * @property-read \App\Models\Tag|null $tag
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereFeaturedEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereFeaturedStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereImageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereIsOutstanding($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereIsScraped($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereIsTranslated($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereIsVerified($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereMediaOutletId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereMunicipalityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle wherePublishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereSourceUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereSummary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereTagId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereViewsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereVisibility($value)
 */
	class NewsArticle extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property string $type
 * @property int|null $target_id
 * @property numeric|null $threshold
 * @property string $delivery_method
 * @property bool $is_silent
 * @property bool $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting whereDeliveryMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting whereIsSilent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting whereTargetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting whereThreshold($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting whereUserId($value)
 */
	class NotificationSetting extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $logo
 * @property string|null $domain
 * @property string|null $contact_email
 * @property string|null $contact_phone
 * @property string|null $primary_color
 * @property string|null $secondary_color
 * @property array<array-key, mixed>|null $css_files
 * @property bool $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AppSetting|null $appSettings
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrganizationFeature> $features
 * @property-read int|null $features_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereContactEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereContactPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereCssFiles($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization wherePrimaryColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereSecondaryColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereUpdatedAt($value)
 */
	class Organization extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $organization_id
 * @property string $feature_key
 * @property bool $enabled_dashboard
 * @property bool $enabled_web
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Organization $organization
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationFeature newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationFeature newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationFeature query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationFeature whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationFeature whereEnabledDashboard($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationFeature whereEnabledWeb($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationFeature whereFeatureKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationFeature whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationFeature whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationFeature whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationFeature whereUpdatedAt($value)
 */
	class OrganizationFeature extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string|null $birth_name
 * @property string $slug
 * @property \Illuminate\Support\Carbon|null $birth_date
 * @property \Illuminate\Support\Carbon|null $death_date
 * @property string|null $birth_place
 * @property string|null $death_place
 * @property int|null $nationality_id
 * @property int|null $language_id
 * @property int|null $image_id
 * @property string $gender
 * @property string|null $official_website
 * @property string|null $wikidata_id
 * @property string|null $wikipedia_url
 * @property string|null $notable_for
 * @property string|null $occupation_summary
 * @property array<array-key, mixed>|null $social_handles
 * @property bool $is_influencer
 * @property int $search_boost
 * @property string|null $short_bio
 * @property string|null $long_bio
 * @property string|null $source_url
 * @property \Illuminate\Support\Carbon|null $last_updated_from_source
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Image|null $image
 * @property-read \App\Models\Language|null $language
 * @property-read \App\Models\Country|null $nationality
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereBirthDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereBirthName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereBirthPlace($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereDeathDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereDeathPlace($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereImageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereIsInfluencer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereLastUpdatedFromSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereLongBio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereNationalityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereNotableFor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereOccupationSummary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereOfficialWebsite($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereSearchBoost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereShortBio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereSocialHandles($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereSourceUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereWikidataId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereWikipediaUrl($value)
 */
	class Person extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $common_name
 * @property string|null $scientific_name
 * @property string $co2_absorption_kg_per_year Approx. annual CO₂ absorption
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Image|null $image
 * @property-read \App\Models\Region|null $nativeRegion
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSpecies newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSpecies newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSpecies query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSpecies whereCo2AbsorptionKgPerYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSpecies whereCommonName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSpecies whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSpecies whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSpecies whereScientificName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSpecies whereUpdatedAt($value)
 */
	class PlantSpecies extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $address
 * @property string|null $type
 * @property string|null $latitude
 * @property string|null $longitude
 * @property int $municipality_id
 * @property string|null $source
 * @property string|null $description
 * @property bool $is_cultural_center
 * @property bool $is_energy_installation
 * @property bool $is_cooperative_office
 * @property array<array-key, mixed>|null $opening_hours
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Municipality $municipality
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $tags
 * @property-read int|null $tags_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointOfInterest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointOfInterest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointOfInterest query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointOfInterest whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointOfInterest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointOfInterest whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointOfInterest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointOfInterest whereIsCooperativeOffice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointOfInterest whereIsCulturalCenter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointOfInterest whereIsEnergyInstallation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointOfInterest whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointOfInterest whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointOfInterest whereMunicipalityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointOfInterest whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointOfInterest whereOpeningHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointOfInterest whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointOfInterest whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointOfInterest whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointOfInterest whereUpdatedAt($value)
 */
	class PointOfInterest extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $short_name
 * @property string|null $unit_code
 * @property string|null $conversion_factor_to_kwh
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceUnit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceUnit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceUnit query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceUnit whereConversionFactorToKwh($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceUnit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceUnit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceUnit whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceUnit whereShortName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceUnit whereUnitCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceUnit whereUpdatedAt($value)
 */
	class PriceUnit extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $category
 * @property int $is_public_facing
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profession newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profession newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profession query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profession whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profession whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profession whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profession whereIsPublicFacing($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profession whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profession whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profession whereUpdatedAt($value)
 */
	class Profession extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $ine_code
 * @property int $autonomous_community_id
 * @property int $country_id
 * @property string|null $latitude
 * @property string|null $longitude
 * @property string|null $area_km2
 * @property int|null $altitude_m
 * @property int|null $timezone_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AutonomousCommunity $autonomousCommunity
 * @property-read \App\Models\Country $country
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Region> $regions
 * @property-read int|null $regions_count
 * @property-read \App\Models\Timezone|null $timezone
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Province newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Province newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Province query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Province whereAltitudeM($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Province whereAreaKm2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Province whereAutonomousCommunityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Province whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Province whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Province whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Province whereIneCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Province whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Province whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Province whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Province whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Province whereTimezoneId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Province whereUpdatedAt($value)
 */
	class Province extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property int $province_id
 * @property int|null $autonomous_community_id
 * @property int $country_id
 * @property string|null $latitude
 * @property string|null $longitude
 * @property string|null $area_km2
 * @property int|null $altitude_m
 * @property int|null $timezone_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AutonomousCommunity|null $autonomousCommunity
 * @property-read \App\Models\Country $country
 * @property-read \App\Models\Province $province
 * @property-read \App\Models\Timezone|null $timezone
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereAltitudeM($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereAreaKm2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereAutonomousCommunityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereProvinceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereTimezoneId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereUpdatedAt($value)
 */
	class Region extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $reciprocal_slug
 * @property string $category
 * @property int|null $degree
 * @property int $gender_specific
 * @property string|null $description
 * @property int $is_symmetrical
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RelationshipType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RelationshipType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RelationshipType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RelationshipType whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RelationshipType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RelationshipType whereDegree($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RelationshipType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RelationshipType whereGenderSpecific($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RelationshipType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RelationshipType whereIsSymmetrical($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RelationshipType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RelationshipType whereReciprocalSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RelationshipType whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RelationshipType whereUpdatedAt($value)
 */
	class RelationshipType extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string|null $url
 * @property string $type
 * @property string|null $source_type_description
 * @property string|null $frequency
 * @property \Illuminate\Support\Carbon|null $last_scraped_at
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrapingSource newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrapingSource newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrapingSource query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrapingSource whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrapingSource whereFrequency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrapingSource whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrapingSource whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrapingSource whereLastScrapedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrapingSource whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrapingSource whereSourceTypeDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrapingSource whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrapingSource whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrapingSource whereUrl($value)
 */
	class ScrapingSource extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $platform
 * @property string $handle
 * @property string $url
 * @property int $person_id
 * @property int|null $followers_count
 * @property int $verified
 * @property int $is_public
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Person $person
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialAccount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialAccount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialAccount query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialAccount whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialAccount whereFollowersCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialAccount whereHandle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialAccount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialAccount whereIsPublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialAccount wherePersonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialAccount wherePlatform($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialAccount whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialAccount whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialAccount whereVerified($value)
 */
	class SocialAccount extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $subject_type
 * @property int $subject_id
 * @property string $key
 * @property string $value
 * @property string $year
 * @property int|null $data_source_id
 * @property string|null $unit
 * @property string|null $confidence_level
 * @property string|null $source_note
 * @property int $is_projection
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\DataSource|null $source
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $subject
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stat newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stat newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stat query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stat whereConfidenceLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stat whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stat whereDataSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stat whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stat whereIsProjection($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stat whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stat whereSourceNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stat whereSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stat whereSubjectType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stat whereUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stat whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stat whereValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stat whereYear($value)
 */
	class Stat extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $data_source_id
 * @property string $status
 * @property \Illuminate\Support\Carbon $started_at
 * @property \Illuminate\Support\Carbon|null $finished_at
 * @property int $processed_items_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\DataSource|null $dataSource
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyncLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyncLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyncLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyncLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyncLog whereDataSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyncLog whereFinishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyncLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyncLog whereProcessedItemsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyncLog whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyncLog whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyncLog whereUpdatedAt($value)
 */
	class SyncLog extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $tag_type
 * @property int $is_searchable
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Anniversary> $anniversaries
 * @property-read int|null $anniversaries_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Event> $events
 * @property-read int|null $events_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Person> $people
 * @property-read int|null $people_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PointOfInterest> $pointOfInterests
 * @property-read int|null $point_of_interests_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Work> $works
 * @property-read int|null $works_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereIsSearchable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereTagType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereUpdatedAt($value)
 */
	class Tag extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagGroup whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagGroup whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagGroup whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagGroup whereUpdatedAt($value)
 */
	class TagGroup extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $offset
 * @property string|null $dst_offset
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Country> $countries
 * @property-read int|null $countries_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Municipality> $municipalities
 * @property-read int|null $municipalities_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Timezone newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Timezone newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Timezone query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Timezone whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Timezone whereDstOffset($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Timezone whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Timezone whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Timezone whereOffset($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Timezone whereUpdatedAt($value)
 */
	class Timezone extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property string|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CooperativeUserMember> $cooperativeMemberships
 * @property-read int|null $cooperative_memberships_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Cooperative> $cooperatives
 * @property-read int|null $cooperatives_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorConfirmedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorRecoveryCodes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $device_name
 * @property string|null $device_type
 * @property string|null $platform
 * @property string|null $browser
 * @property string|null $ip_address
 * @property string|null $token
 * @property int $notifications_enabled
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserDevice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserDevice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserDevice query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserDevice whereBrowser($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserDevice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserDevice whereDeviceName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserDevice whereDeviceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserDevice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserDevice whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserDevice whereNotificationsEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserDevice wherePlatform($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserDevice whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserDevice whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserDevice whereUserId($value)
 */
	class UserDevice extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property string $related_type
 * @property int $related_id
 * @property string $content
 * @property string $type
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $related
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserGeneratedContent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserGeneratedContent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserGeneratedContent query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserGeneratedContent whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserGeneratedContent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserGeneratedContent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserGeneratedContent whereRelatedId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserGeneratedContent whereRelatedType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserGeneratedContent whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserGeneratedContent whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserGeneratedContent whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserGeneratedContent whereUserId($value)
 */
	class UserGeneratedContent extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $address
 * @property int $municipality_id
 * @property string $latitude
 * @property string $longitude
 * @property int|null $capacity
 * @property string|null $description
 * @property string $venue_type
 * @property string $venue_status
 * @property int $is_verified
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Event> $events
 * @property-read int|null $events_count
 * @property-read \App\Models\Municipality $municipality
 * @property-read \App\Models\VenueType|null $venueType
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Venue newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Venue newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Venue query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Venue whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Venue whereCapacity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Venue whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Venue whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Venue whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Venue whereIsVerified($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Venue whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Venue whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Venue whereMunicipalityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Venue whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Venue whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Venue whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Venue whereVenueStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Venue whereVenueType($value)
 */
	class Venue extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Venue> $venues
 * @property-read int|null $venues_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VenueType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VenueType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VenueType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VenueType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VenueType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VenueType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VenueType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VenueType whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VenueType whereUpdatedAt($value)
 */
	class VenueType extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Color> $colors
 * @property-read int|null $colors_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Font> $fonts
 * @property-read int|null $fonts_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VisualIdentity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VisualIdentity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VisualIdentity query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VisualIdentity whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VisualIdentity whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VisualIdentity whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VisualIdentity whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VisualIdentity whereUpdatedAt($value)
 */
	class VisualIdentity extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon $datetime
 * @property string $location
 * @property float|null $temperature
 * @property float|null $humidity
 * @property float|null $cloud_coverage
 * @property float|null $solar_irradiance
 * @property float|null $wind_speed
 * @property float|null $precipitation
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherAndSolarData newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherAndSolarData newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherAndSolarData query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherAndSolarData whereCloudCoverage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherAndSolarData whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherAndSolarData whereDatetime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherAndSolarData whereHumidity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherAndSolarData whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherAndSolarData whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherAndSolarData wherePrecipitation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherAndSolarData whereSolarIrradiance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherAndSolarData whereTemperature($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherAndSolarData whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherAndSolarData whereWindSpeed($value)
 */
	class WeatherAndSolarData extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $type
 * @property string|null $description
 * @property int|null $release_year
 * @property int|null $person_id
 * @property string|null $genre
 * @property int|null $language_id
 * @property int|null $link_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Language|null $language
 * @property-read \App\Models\Link|null $link
 * @property-read \App\Models\Person|null $person
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Work newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Work newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Work query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Work whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Work whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Work whereGenre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Work whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Work whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Work whereLinkId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Work wherePersonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Work whereReleaseYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Work whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Work whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Work whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Work whereUpdatedAt($value)
 */
	class Work extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $climate_zone
 * @property string|null $description
 * @property string|null $average_heating_demand
 * @property string|null $average_cooling_demand
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ZoneClimate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ZoneClimate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ZoneClimate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ZoneClimate whereAverageCoolingDemand($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ZoneClimate whereAverageHeatingDemand($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ZoneClimate whereClimateZone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ZoneClimate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ZoneClimate whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ZoneClimate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ZoneClimate whereUpdatedAt($value)
 */
	class ZoneClimate extends \Eloquent {}
}

