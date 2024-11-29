<?php

namespace Workdo\Webhook\Providers;

use App\Events\CompanySettingEvent;
use App\Events\CompanySettingMenuEvent;
use App\Events\CreateInvoice;
use App\Events\CreatePaymentInvoice;
use App\Events\CreateProposal;
use App\Events\CreateUser;
use App\Events\PaymentDestroyInvoice;
use App\Events\SentInvoice;
use App\Events\StatusChangeProposal;
use App\Events\SuperAdminMenuEvent;
use App\Events\SuperAdminSettingEvent;
use App\Events\SuperAdminSettingMenuEvent;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as Provider;
use Workdo\AamarPay\Events\AamarPaymentStatus;
use Workdo\Account\Events\CreateBill;
use Workdo\Account\Events\CreateCustomer;
use Workdo\Account\Events\CreatePayment;
use Workdo\Account\Events\CreateRevenue;
use Workdo\Account\Events\CreateVendor;
use Workdo\AgricultureManagement\Events\AssignActivityCultivation;
use Workdo\AgricultureManagement\Events\ChangeStatusAgricultureCultivation;
use Workdo\AgricultureManagement\Events\CreateAgricultureActivities;
use Workdo\AgricultureManagement\Events\CreateAgricultureCanal;
use Workdo\AgricultureManagement\Events\CreateAgricultureClaimType;
use Workdo\AgricultureManagement\Events\CreateAgricultureCrop;
use Workdo\AgricultureManagement\Events\CreateAgricultureCultivation;
use Workdo\AgricultureManagement\Events\CreateAgricultureCycles;
use Workdo\AgricultureManagement\Events\CreateAgricultureDepartment;
use Workdo\AgricultureManagement\Events\CreateAgricultureEquipment;
use Workdo\AgricultureManagement\Events\CreateAgriculturefleet;
use Workdo\AgricultureManagement\Events\CreateAgricultureOffices;
use Workdo\AgricultureManagement\Events\CreateAgricultureProcess;
use Workdo\AgricultureManagement\Events\CreateAgricultureSeason;
use Workdo\AgricultureManagement\Events\CreateAgricultureSeasonType;
use Workdo\AgricultureManagement\Events\CreateAgricultureServiceProduct;
use Workdo\AgricultureManagement\Events\CreateAgricultureServices;
use Workdo\AgricultureManagement\Events\CreateAgricultureUser;
use Workdo\Appointment\Events\AppointmentStatus;
use Workdo\Appointment\Events\CreateAppointments;
use Workdo\BeautySpaManagement\Events\CreateBeautyBooking;
use Workdo\BeautySpaManagement\Events\CreateBeautyService;
use Workdo\BeautySpaManagement\Events\UpdateWorkingHours;
use Workdo\Benefit\Events\BenefitPaymentStatus;
use Workdo\BeverageManagement\Events\CreateBillItemMaterial;
use Workdo\BeverageManagement\Events\CreateBillOfMaterial;
use Workdo\BeverageManagement\Events\CreateCollectionCenter;
use Workdo\BeverageManagement\Events\CreateManufacturing;
use Workdo\BeverageManagement\Events\CreatePackaging;
use Workdo\BeverageManagement\Events\CreateRawMaterial;
use Workdo\BusinessProcessMapping\Events\CreateBusinessProcessMapping;
use Workdo\CallHub\Events\CreateCallList;
use Workdo\CarDealership\Events\CreateCarPurchase;
use Workdo\CarDealership\Events\CreateCarSale;
use Workdo\CarDealership\Events\CreateCategory;
use Workdo\CarDealership\Events\CreateDealershipProduct;
use Workdo\CarDealership\Events\CreatePaymentCarPurchase;
use Workdo\CarDealership\Events\CreatePaymentCarSale;
use Workdo\CarDealership\Events\CreateTax;
use Workdo\Cashfree\Events\CashfreePaymentStatus;
use Workdo\CateringManagement\Events\CateringCreateEvent;
use Workdo\CateringManagement\Events\CreateCateringCustomer;
use Workdo\CateringManagement\Events\CreateCateringInvoice;
use Workdo\CateringManagement\Events\CreateEventDetail;
use Workdo\CateringManagement\Events\CreateMenuSelection;
use Workdo\CateringManagement\Events\CreateSelection;
use Workdo\ChildcareManagement\Events\CreateAvtivity;
use Workdo\ChildcareManagement\Events\CreateChild;
use Workdo\ChildcareManagement\Events\CreateChildAttendance;
use Workdo\ChildcareManagement\Events\CreateClass;
use Workdo\ChildcareManagement\Events\CreateFee;
use Workdo\ChildcareManagement\Events\CreateFeePayment;
use Workdo\ChildcareManagement\Events\CreateInquiry;
use Workdo\ChildcareManagement\Events\CreateNutrition;
use Workdo\ChildcareManagement\Events\CreateParent;
use Workdo\ChildcareManagement\Events\UpdateChidcare;
use Workdo\CleaningManagement\Events\CreateCleaningBooking;
use Workdo\CleaningManagement\Events\CreateCleaningInvoice;
use Workdo\CleaningManagement\Events\CreateCleaningTeam;
use Workdo\CMMS\Events\CreateCmmspos;
use Workdo\CMMS\Events\CreateComponent;
use Workdo\CMMS\Events\CreateLocation;
use Workdo\CMMS\Events\CreatePms;
use Workdo\CMMS\Events\CreateSupplier;
use Workdo\CMMS\Events\CreateWorkorder;
use Workdo\CMMS\Events\CreateWorkrequest;
use Workdo\Coingate\Events\CoingatePaymentStatus;
use Workdo\Commission\Events\CreateCommissionPlan;
use Workdo\Commission\Events\CreateCommissionReceipt;
use Workdo\ConsignmentManagement\Events\CreateConsignment;
use Workdo\ConsignmentManagement\Events\CreateProduct;
use Workdo\ConsignmentManagement\Events\CreatePurchaseOrder;
use Workdo\ConsignmentManagement\Events\CreateSaleOrder;
use Workdo\ContractTemplate\Events\CopyContractTemplate;
use Workdo\ContractTemplate\Events\CreateContractTemplate;
use Workdo\Contract\Events\CreateContract;
use Workdo\CourierManagement\Events\Courierbranchcreate;
use Workdo\CourierManagement\Events\Couriercreate;
use Workdo\CourierManagement\Events\Courierpackagecategorycreate;
use Workdo\CourierManagement\Events\Courierservicetypecreate;
use Workdo\CourierManagement\Events\Couriertrackingstatuscreate;
use Workdo\CourierManagement\Events\Manualpaymentdatastore;
use Workdo\DairyCattleManagement\Events\CreateAnimal;
use Workdo\DairyCattleManagement\Events\CreateBreeding;
use Workdo\DairyCattleManagement\Events\CreateDailyMilkSheet;
use Workdo\DairyCattleManagement\Events\CreateHealth;
use Workdo\DairyCattleManagement\Events\CreateMilkInventory;
use Workdo\DairyCattleManagement\Events\CreateWeight;
use Workdo\Documents\Events\CreateDocuments;
use Workdo\Documents\Events\CreateDocumentsType;
use Workdo\DocumentTemplate\Events\ConvertDocumentTemplate;
use Workdo\DocumentTemplate\Events\CreateDocumentTemplate;
use Workdo\DocumentTemplate\Events\DuplicateDocumentTemplate;
use Workdo\DoubleEntry\Events\CreateJournalAccount;
use Workdo\Exam\Events\CreateExamGrade;
use Workdo\Exam\Events\CreateExamHall;
use Workdo\Exam\Events\CreateExamHallReceipt;
use Workdo\Exam\Events\CreateExamList;
use Workdo\Exam\Events\CreateExamTimeTable;
use Workdo\Exam\Events\CreateManageMarks;
use Workdo\FileSharing\Events\CreateFile;
use Workdo\FixEquipment\Events\CreateAccessories;
use Workdo\FixEquipment\Events\CreateAsset;
use Workdo\FixEquipment\Events\CreateAudit;
use Workdo\FixEquipment\Events\CreateCategory as CreateFixCategory;
use Workdo\FixEquipment\Events\CreateComponent as CreateFixEquipmentComponents;
use Workdo\FixEquipment\Events\CreateConsumables;
use Workdo\FixEquipment\Events\CreateDepreciation;
use Workdo\FixEquipment\Events\CreateLicence;
use Workdo\FixEquipment\Events\CreateLocation as CreateFixEquipmentLocation;
use Workdo\FixEquipment\Events\CreateMaintenance;
use Workdo\FixEquipment\Events\CreateManufacturer;
use Workdo\FixEquipment\Events\CreatePreDefinedKit;
use Workdo\FixEquipment\Events\CreateStatus;
use Workdo\Fleet\Events\CreateBooking;
use Workdo\Fleet\Events\CreateDriver;
use Workdo\Fleet\Events\CreateFleetCustomer;
use Workdo\Fleet\Events\CreateFleetPayment;
use Workdo\Fleet\Events\CreateFuel;
use Workdo\Fleet\Events\CreateInsurance as CreateFleetInsurance;
use Workdo\Fleet\Events\CreateMaintenances;
use Workdo\Fleet\Events\CreateVehicle;
use Workdo\Flutterwave\Events\FlutterwavePaymentStatus;
use Workdo\FreightManagementSystem\Events\CreateFreightBookingRequest;
use Workdo\FreightManagementSystem\Events\CreateFreightContainer;
use Workdo\FreightManagementSystem\Events\CreateFreightCustomer;
use Workdo\FreightManagementSystem\Events\CreateFreightPrice;
use Workdo\FreightManagementSystem\Events\CreateFreightService;
use Workdo\FreightManagementSystem\Events\CreateFreightShippingInvoice;
use Workdo\FreightManagementSystem\Events\CreateFreightShippingOrder;
use Workdo\FreightManagementSystem\Events\CreateFreightShippingRoute;
use Workdo\FreightManagementSystem\Events\CreateOrUpdateFreightShippingService;
use Workdo\FreightManagementSystem\Events\UpdateFreightShipping;
use Workdo\GarageManagement\Events\CreateFuelType;
use Workdo\GarageManagement\Events\CreateGarageCategory;
use Workdo\GarageManagement\Events\CreateGarageVehicle;
use Workdo\GarageManagement\Events\CreateJobCard;
use Workdo\GarageManagement\Events\CreateService;
use Workdo\GarageManagement\Events\CreatevehicleBrand;
use Workdo\GarageManagement\Events\CreateVehicleColor;
use Workdo\GarageManagement\Events\CreatevehicleType;
use Workdo\GymManagement\Events\CreateBodyPart;
use Workdo\GymManagement\Events\CreateDiet;
use Workdo\GymManagement\Events\CreateEquipment;
use Workdo\GymManagement\Events\CreateExercise;
use Workdo\GymManagement\Events\CreateGymTrainer;
use Workdo\GymManagement\Events\CreateMeasurement;
use Workdo\GymManagement\Events\CreateMembershipPlan;
use Workdo\GymManagement\Events\CreateSkill;
use Workdo\GymManagement\Events\CreateWorkoutPlan;
use Workdo\Holidayz\Events\CreateBookingCoupon;
use Workdo\Holidayz\Events\CreateHotel;
use Workdo\Holidayz\Events\CreateHotelCustomer;
use Workdo\Holidayz\Events\CreateHotelService;
use Workdo\Holidayz\Events\CreatePageOption;
use Workdo\Holidayz\Events\CreateRoom;
use Workdo\Holidayz\Events\CreateRoomBooking;
use Workdo\Holidayz\Events\CreateRoomFacility;
use Workdo\Holidayz\Events\CreateRoomFeature;
use Workdo\Holidayz\Events\UpdateHotel;
use Workdo\HospitalManagement\Events\CreateBedType;
use Workdo\HospitalManagement\Events\CreateDoctor;
use Workdo\HospitalManagement\Events\CreateHospitalAppointment;
use Workdo\HospitalManagement\Events\CreateHospitalBed;
use Workdo\HospitalManagement\Events\CreateHospitalMedicine;
use Workdo\HospitalManagement\Events\CreateMedicalRecords;
use Workdo\HospitalManagement\Events\CreateMedicineCategory;
use Workdo\HospitalManagement\Events\CreatePatient;
use Workdo\HospitalManagement\Events\CreateSpecialization;
use Workdo\HospitalManagement\Events\CreateWard;
use Workdo\Hrm\Events\CreateAnnouncement;
use Workdo\Hrm\Events\CreateAward;
use Workdo\Hrm\Events\CreateCompanyPolicy;
use Workdo\Hrm\Events\CreateEvent;
use Workdo\Hrm\Events\CreateHolidays;
use Workdo\Hrm\Events\CreateMonthlyPayslip;
use Workdo\InnovationCenter\Events\CreateCategory as CreateChallengeCategory;
use Workdo\InnovationCenter\Events\CreateChallenge;
use Workdo\InnovationCenter\Events\CreateCreativity;
use Workdo\InnovationCenter\Events\CreateCreativityStage;
use Workdo\InnovationCenter\Events\CreateCreativityStatus;
use Workdo\InsuranceManagement\Events\AcceptinsuranceClaim;
use Workdo\InsuranceManagement\Events\CreateInsurance;
use Workdo\InsuranceManagement\Events\CreateinsuranceClaim;
use Workdo\InsuranceManagement\Events\CreateinsuranceClaimPayment;
use Workdo\InsuranceManagement\Events\CreateinsuranceInvoice;
use Workdo\InsuranceManagement\Events\CreatePolicy;
use Workdo\InsuranceManagement\Events\CreatepolicyType;
use Workdo\InsuranceManagement\Events\RejectinsuranceClaim;
use Workdo\InsuranceManagement\Events\SendInsuraceMail;
use Workdo\Internalknowledge\Events\CreateArticle;
use Workdo\Internalknowledge\Events\CreateBook;
use Workdo\Iyzipay\Events\IyzipayPaymentStatus;
use Workdo\LaundryManagement\Events\LaundryLocationCreate;
use Workdo\LaundryManagement\Events\LaundryRequestCreate;
use Workdo\LaundryManagement\Events\LaundryServicesCreate;
use Workdo\Lead\Events\CreateDeal;
use Workdo\Lead\Events\CreateLead;
use Workdo\Lead\Events\DealMoved;
use Workdo\Lead\Events\LeadConvertDeal;
use Workdo\Lead\Events\LeadMoved;
use Workdo\LegalCaseManagement\Events\CreateAdvocate;
use Workdo\LegalCaseManagement\Events\CreateCase;
use Workdo\LegalCaseManagement\Events\CreateCaseInitiator;
use Workdo\LegalCaseManagement\Events\CreateCourt;
use Workdo\LegalCaseManagement\Events\CreateDivision;
use Workdo\LegalCaseManagement\Events\CreateExpense;
use Workdo\LegalCaseManagement\Events\CreateFeePayment as CreateFeeCasePayment;
use Workdo\LegalCaseManagement\Events\CreateFeeReciept;
use Workdo\LegalCaseManagement\Events\CreateFeeRecieve;
use Workdo\LegalCaseManagement\Events\CreateHearing;
use Workdo\LegalCaseManagement\Events\CreateHighCourt;
use Workdo\LMS\Events\CreateBlog;
use Workdo\LMS\Events\CreateCourse;
use Workdo\LMS\Events\CreateCustomPage;
use Workdo\LMS\Events\CreateRatting;
use Workdo\MachineRepairManagement\Events\CreateDiagnosis;
use Workdo\MachineRepairManagement\Events\CreateMachine;
use Workdo\MachineRepairManagement\Events\CreatePaymentDiagnosis;
use Workdo\MachineRepairManagement\Events\CreateRepairRequest;
use Workdo\MedicalLabManagement\Events\CreateLabPatient;
use Workdo\MedicalLabManagement\Events\CreateLabRequest;
use Workdo\MedicalLabManagement\Events\CreateLabTest;
use Workdo\MedicalLabManagement\Events\CreateMedicalAppoinment;
use Workdo\MedicalLabManagement\Events\CreatePatientCard;
use Workdo\MedicalLabManagement\Events\CreateTestContent;
use Workdo\MedicalLabManagement\Events\CreateTestUnit;
use Workdo\MeetingHub\Events\CreateMeeingHubMeeting;
use Workdo\MeetingHub\Events\CreateMeeingHubMeetingMinute;
use Workdo\MeetingHub\Events\CreateMeeingHubNote;
use Workdo\MeetingHub\Events\CreateMeeingHubTask;
use Workdo\Mercado\Events\MercadoPaymentStatus;
use Workdo\MobileServiceManagement\Events\MobileServiceAssignTechnician;
use Workdo\MobileServiceManagement\Events\MobileServiceCreate;
use Workdo\MobileServiceManagement\Events\MobileServicePendingRequestAccept;
use Workdo\MobileServiceManagement\Events\MobileServicePendingRequestReject;
use Workdo\MobileServiceManagement\Events\MobileServiceRequestInvoiceCreate;
use Workdo\MobileServiceManagement\Events\MobileServiceRequestTrackingStatusCreate;
use Workdo\Mollie\Events\MolliePaymentStatus;
use Workdo\MovieShowBookingSystem\Events\CreateCastType;
use Workdo\MovieShowBookingSystem\Events\CreateCertificate;
use Workdo\MovieShowBookingSystem\Events\CreateMovieCast;
use Workdo\MovieShowBookingSystem\Events\CreateMovieCrew;
use Workdo\MovieShowBookingSystem\Events\CreateMovieEvent;
use Workdo\MovieShowBookingSystem\Events\CreateMovieShow;
use Workdo\MovieShowBookingSystem\Events\CreateSeatingTemplate;
use Workdo\MovieShowBookingSystem\Events\CreateSeatingTemplateDetail;
use Workdo\MovieShowBookingSystem\Events\CreateShowType;
use Workdo\MusicInstitute\Events\CreateMusicClass;
use Workdo\MusicInstitute\Events\CreateMusicInstrument;
use Workdo\MusicInstitute\Events\CreateMusicLesson;
use Workdo\MusicInstitute\Events\CreateMusicStudent;
use Workdo\MusicInstitute\Events\CreateMusicTeacher;
use Workdo\Newspaper\Events\ChangeStatusNewspaperInvoice;
use Workdo\Newspaper\Events\CreateNewspaper;
use Workdo\Newspaper\Events\CreateNewspaperAds;
use Workdo\Newspaper\Events\CreateNewspaperAgent;
use Workdo\Newspaper\Events\CreateNewspaperCategory;
use Workdo\Newspaper\Events\CreateNewspaperDistributions;
use Workdo\Newspaper\Events\CreateNewspaperInvoice;
use Workdo\Newspaper\Events\CreateNewspaperJournalist;
use Workdo\Newspaper\Events\CreateNewspaperJournalistInfo;
use Workdo\Newspaper\Events\CreateNewspaperJournalistType;
use Workdo\Newspaper\Events\CreateNewspaperTax;
use Workdo\Newspaper\Events\CreateNewspaperType;
use Workdo\Newspaper\Events\CreateNewspaperVarient;
use Workdo\PaiementPro\Events\PaiementProPaymentStatus;
use Workdo\ParkingManagement\Events\CreateParking;
use Workdo\ParkingManagement\Events\CreateSlot;
use Workdo\ParkingManagement\Events\CreateSlotType;
use Workdo\Payfast\Events\PayfastPaymentStatus;
use Workdo\PayHere\Events\PayHerePaymentStatus;
use Workdo\Paypal\Events\PaypalPaymentStatus;
use Workdo\Paystack\Events\PaystackPaymentStatus;
use Workdo\PayTab\Events\PaytabPaymentStatus;
use Workdo\Paytm\Events\PaytmPaymentStatus;
use Workdo\PayTR\Events\PaytrPaymentStatus;
use Workdo\PharmacyManagement\Events\MedicineCategoryCreate;
use Workdo\PharmacyManagement\Events\MedicineCreate;
use Workdo\PharmacyManagement\Events\MedicineTypeCreate;
use Workdo\PharmacyManagement\Events\PharmacyBillCreate;
use Workdo\PharmacyManagement\Events\PharmacyInvoiceCreate;
use Workdo\PhonePe\Events\PhonePePaymentStatus;
use Workdo\Portfolio\Events\CreatePortfolio;
use Workdo\Portfolio\Events\PortfolioCategoryCreate;
use Workdo\Portfolio\Events\UpdatePortfolioStatus;
use Workdo\Pos\Events\CreatePurchase;
use Workdo\Pos\Events\CreateWarehouse;
use Workdo\PropertyManagement\Events\CreateProperty;
use Workdo\PropertyManagement\Events\CreatePropertyInvoice;
use Workdo\PropertyManagement\Events\CreatePropertyInvoicePayment;
use Workdo\PropertyManagement\Events\CreatePropertyUnit;
use Workdo\PropertyManagement\Events\CreateTenant;
use Workdo\Razorpay\Events\RazorpayPaymentStatus;
use Workdo\Recruitment\Events\ConvertToEmployee;
use Workdo\Recruitment\Events\CreateInterviewSchedule;
use Workdo\Recruitment\Events\CreateJob;
use Workdo\Recruitment\Events\CreateJobApplication;
use Workdo\RentalManagement\Events\CreateRental;
use Workdo\RentalManagement\Events\DuplicateRental;
use Workdo\RepairManagementSystem\Events\CreateRepairOrderRequest;
use Workdo\RepairManagementSystem\Events\CreateRepairPart;
use Workdo\RepairManagementSystem\Events\CretaeRepairInvoice;
use Workdo\Retainer\Events\CreatePaymentRetainer;
use Workdo\Retainer\Events\CreateRetainer;
use Workdo\Retainer\Events\PaymentDestroyRetainer;
use Workdo\Rotas\Events\CreateAvailability;
use Workdo\Rotas\Events\CreateRota;
use Workdo\Sage\Events\CreateLedgerAccount;
use Workdo\SalesAgent\Events\SalesAgentCreate;
use Workdo\SalesAgent\Events\SalesAgentOrderCreate;
use Workdo\SalesAgent\Events\SalesAgentProgramCreate;
use Workdo\SalesAgent\Events\SalesAgentRequestAccept;
use Workdo\SalesAgent\Events\SalesAgentRequestReject;
use Workdo\Sales\Events\CreateMeeting;
use Workdo\Sales\Events\CreateQuote;
use Workdo\Sales\Events\CreateSalesInvoice;
use Workdo\Sales\Events\CreateSalesOrder;
use Workdo\School\Events\CreateAdmission;
use Workdo\School\Events\CreateClassroom;
use Workdo\School\Events\CreateSchoolEmployee;
use Workdo\School\Events\CreateSchoolHomework;
use Workdo\School\Events\CreateSchoolParent;
use Workdo\School\Events\CreateSchoolStudent;
use Workdo\School\Events\CreateSubject;
use Workdo\School\Events\CreateTimetable;
use Workdo\SideMenuBuilder\Events\CreateSideMenuBuilder;
use Workdo\Skrill\Events\SkrillPaymentStatus;
use Workdo\Spreadsheet\Events\CreateSpreadsheet;
use Workdo\SSPay\Events\SSpayPaymentStatus;
use Workdo\Stripe\Events\StripePaymentStatus;
use Workdo\SupportTicket\Events\CreatePublicTicket;
use Workdo\SupportTicket\Events\CreateTicket;
use Workdo\SupportTicket\Events\ReplyPublicTicket;
use Workdo\SupportTicket\Events\ReplyTicket;
use Workdo\Taskly\Events\CreateBug;
use Workdo\Taskly\Events\CreateMilestone;
use Workdo\Taskly\Events\CreateProject;
use Workdo\Taskly\Events\CreateTask;
use Workdo\Taskly\Events\CreateTaskComment;
use Workdo\Taskly\Events\UpdateTaskStage;
use Workdo\TimeTracker\Events\CreateTimeTracker;
use Workdo\ToDo\Events\CreateToDo;
use Workdo\ToDo\Events\ToDoStageSystemSetup;
use Workdo\TourTravelManagement\Events\CreatePersonDetail;
use Workdo\TourTravelManagement\Events\CreateSeason;
use Workdo\TourTravelManagement\Events\CreateTour;
use Workdo\TourTravelManagement\Events\CreateTourBooking;
use Workdo\TourTravelManagement\Events\CreateTourBookingPayment;
use Workdo\TourTravelManagement\Events\CreateTourDetail;
use Workdo\TourTravelManagement\Events\CreateTourInquiry;
use Workdo\TourTravelManagement\Events\CreateTransportType;
use Workdo\Toyyibpay\Events\ToyyibpayPaymentStatus;
use Workdo\Training\Events\CreateTrainer;
use Workdo\Training\Events\CreateTraining;
use Workdo\VCard\Events\CreateAppointment;
use Workdo\VCard\Events\CreateBusiness;
use Workdo\VCard\Events\CreateContact;
use Workdo\VehicleBookingManagement\Events\CreateVehicleBooking;
use Workdo\VehicleBookingManagement\Events\CreateVehicleRoute;
use Workdo\VehicleInspectionManagement\Events\CreateDefectsAndRepairs;
use Workdo\VehicleInspectionManagement\Events\CreateInspectionList;
use Workdo\VehicleInspectionManagement\Events\CreateInspectionRequest;
use Workdo\VehicleInspectionManagement\Events\CreateInspectionVehicle;
use Workdo\VideoHub\Events\CreateVideoHubComment;
use Workdo\VideoHub\Events\CreateVideoHubVideo;
use Workdo\VisitorManagement\Events\CreateVisitor;
use Workdo\VisitorManagement\Events\CreateVisitReason;
use Workdo\WasteManagement\Events\WastecollectionConvertedToTrip;
use Workdo\WasteManagement\Events\WasteCollectionRequestAccept;
use Workdo\WasteManagement\Events\WasteCollectionRequestCreate;
use Workdo\WasteManagement\Events\WasteCollectionRequestReject;
use Workdo\WasteManagement\Events\WasteInspectionStatusUpdate;
use Workdo\Webhook\Listeners\AcceptInsuraceLis;
use Workdo\Webhook\Listeners\AcceptinsuranceClaimLis;
use Workdo\Webhook\Listeners\AppointmentStatusLis;
use Workdo\Webhook\Listeners\AssignActivityCultivationLis;
use Workdo\Webhook\Listeners\CateringCreateEventLis;
use Workdo\Webhook\Listeners\ChangeStatusAgricultureCultivationLis;
use Workdo\Webhook\Listeners\ChangeStatusNewspaperInvoiceLis;
use Workdo\Webhook\Listeners\CompanyPaymentLis;
use Workdo\Webhook\Listeners\CompanySettingListener;
use Workdo\Webhook\Listeners\CompanySettingMenuListener;
use Workdo\Webhook\Listeners\ConvertDocumentTemplateLis;
use Workdo\Webhook\Listeners\ConvertToEmployeeLis;
use Workdo\Webhook\Listeners\CopyContractTemplateLis;
use Workdo\Webhook\Listeners\CreateAccessoriesLis;
use Workdo\Webhook\Listeners\CreateAdmissionLis;
use Workdo\Webhook\Listeners\CreateAdvocateLis;
use Workdo\Webhook\Listeners\CreateAgricultureActivitiesLis;
use Workdo\Webhook\Listeners\CreateAgricultureCanalLis;
use Workdo\Webhook\Listeners\CreateAgricultureClaimTypeLis;
use Workdo\Webhook\Listeners\CreateAgricultureCropLis;
use Workdo\Webhook\Listeners\CreateAgricultureCultivationLis;
use Workdo\Webhook\Listeners\CreateAgricultureCyclesLis;
use Workdo\Webhook\Listeners\CreateAgricultureDepartmentLis;
use Workdo\Webhook\Listeners\CreateAgricultureEquipmentLis;
use Workdo\Webhook\Listeners\CreateAgriculturefleetLis;
use Workdo\Webhook\Listeners\CreateAgricultureOfficesLis;
use Workdo\Webhook\Listeners\CreateAgricultureProcessLis;
use Workdo\Webhook\Listeners\CreateAgricultureSeasonLis;
use Workdo\Webhook\Listeners\CreateAgricultureSeasonTypeLis;
use Workdo\Webhook\Listeners\CreateAgricultureServiceProductLis;
use Workdo\Webhook\Listeners\CreateAgricultureServicesLis;
use Workdo\Webhook\Listeners\CreateAgricultureUserLis;
use Workdo\Webhook\Listeners\CreateAnimalLis;
use Workdo\Webhook\Listeners\CreateAnnouncementLis;
use Workdo\Webhook\Listeners\CreateAppointmentLis;
use Workdo\Webhook\Listeners\CreateAppointmentsLis;
use Workdo\Webhook\Listeners\CreateArticleLis;
use Workdo\Webhook\Listeners\CreateAuditLis;
use Workdo\Webhook\Listeners\CreateAvailabilityLis;
use Workdo\Webhook\Listeners\CreateAvtivityLis;
use Workdo\Webhook\Listeners\CreateAwardLis;
use Workdo\Webhook\Listeners\CreateBeautyBookingLis;
use Workdo\Webhook\Listeners\CreateBeautyServiceLis;
use Workdo\Webhook\Listeners\CreateBedTypeLis;
use Workdo\Webhook\Listeners\CreateBillItemMaterialLis;
use Workdo\Webhook\Listeners\CreateBillLis;
use Workdo\Webhook\Listeners\CreateBillOfMaterialLis;
use Workdo\Webhook\Listeners\CreateBlogLis;
use Workdo\Webhook\Listeners\CreateBodyPartLis;
use Workdo\Webhook\Listeners\CreateBookingCouponLis;
use Workdo\Webhook\Listeners\CreateBookingLis;
use Workdo\Webhook\Listeners\CreateBookLis;
use Workdo\Webhook\Listeners\CreateBreedingLis;
use Workdo\Webhook\Listeners\CreateBugLis;
use Workdo\Webhook\Listeners\CreateBusinessLis;
use Workdo\Webhook\Listeners\CreateBusinessProcessMappingLis;
use Workdo\Webhook\Listeners\CreateCallListLis;
use Workdo\Webhook\Listeners\CreateCarPurchaseLis;
use Workdo\Webhook\Listeners\CreateCarSaleLis;
use Workdo\Webhook\Listeners\CreateCaseInitiatorLis;
use Workdo\Webhook\Listeners\CreateCaseLis;
use Workdo\Webhook\Listeners\CreateCastTypeLis;
use Workdo\Webhook\Listeners\CreateCategoryLis;
use Workdo\Webhook\Listeners\CreateCateringCustomerLis;
use Workdo\Webhook\Listeners\CreateCateringInvoiceLis;
use Workdo\Webhook\Listeners\CreateCertificateLis;
use Workdo\Webhook\Listeners\CreateChallengeCategoryLis;
use Workdo\Webhook\Listeners\CreateChallengeLis;
use Workdo\Webhook\Listeners\CreateChildAttendanceLis;
use Workdo\Webhook\Listeners\CreateChildLis;
use Workdo\Webhook\Listeners\CreateClassLis;
use Workdo\Webhook\Listeners\CreateClassroomLis;
use Workdo\Webhook\Listeners\CreateCleaningBookingLis;
use Workdo\Webhook\Listeners\CreateCleaningInvoiceLis;
use Workdo\Webhook\Listeners\CreateCleaningTeamLis;
use Workdo\Webhook\Listeners\CreateCmmsposLis;
use Workdo\Webhook\Listeners\CreateCollectionCenterLis;
use Workdo\Webhook\Listeners\CreateCommissionPlanLis;
use Workdo\Webhook\Listeners\CreateCommissionReceiptLis;
use Workdo\Webhook\Listeners\CreateCompanyPolicyLis;
use Workdo\Webhook\Listeners\CreateComponentLis;
use Workdo\Webhook\Listeners\CreateConsignmentLis;
use Workdo\Webhook\Listeners\CreateConsignmentProduct;
use Workdo\Webhook\Listeners\CreateContactLis;
use Workdo\Webhook\Listeners\CreateContractLis;
use Workdo\Webhook\Listeners\CreateContractTemplateLis;
use Workdo\Webhook\Listeners\CreateCourieLis;
use Workdo\Webhook\Listeners\CreateCourierbranchLis;
use Workdo\Webhook\Listeners\CreateCourierManualPayment;
use Workdo\Webhook\Listeners\CreateCourierPackageCategoryLis;
use Workdo\Webhook\Listeners\CreateCourierServiceType;
use Workdo\Webhook\Listeners\CreateCourierTrackingStatusLis;
use Workdo\Webhook\Listeners\CreateCourseLis;
use Workdo\Webhook\Listeners\CreateCourtLis;
use Workdo\Webhook\Listeners\CreateCreativityLis;
use Workdo\Webhook\Listeners\CreateCreativityStageLis;
use Workdo\Webhook\Listeners\CreateCreativityStatusLis;
use Workdo\Webhook\Listeners\CreateCustomerLis;
use Workdo\Webhook\Listeners\CreateCustomPageLis;
use Workdo\Webhook\Listeners\CreateDailyMilkSheetLis;
use Workdo\Webhook\Listeners\CreateDealershipProductLis;
use Workdo\Webhook\Listeners\CreateDealLis;
use Workdo\Webhook\Listeners\CreateDefectsAndRepairsLis;
use Workdo\Webhook\Listeners\CreateDepreciationLis;
use Workdo\Webhook\Listeners\CreateDiagnosisLis;
use Workdo\Webhook\Listeners\CreateDietLis;
use Workdo\Webhook\Listeners\CreateDivisionLis;
use Workdo\Webhook\Listeners\CreateDoctorLis;
use Workdo\Webhook\Listeners\CreateDocumentLis;
use Workdo\Webhook\Listeners\CreateDocumentTemplateLis;
use Workdo\Webhook\Listeners\CreateDocumentTypeLis;
use Workdo\Webhook\Listeners\CreateDriverLis;
use Workdo\Webhook\Listeners\CreateEquipmentLis;
use Workdo\Webhook\Listeners\CreateEventDetailLis;
use Workdo\Webhook\Listeners\CreateEventLis;
use Workdo\Webhook\Listeners\CreateExamGradeLis;
use Workdo\Webhook\Listeners\CreateExamHallLis;
use Workdo\Webhook\Listeners\CreateExamHallReceiptLis;
use Workdo\Webhook\Listeners\CreateExamListLis;
use Workdo\Webhook\Listeners\CreateExamTimeTableLis;
use Workdo\Webhook\Listeners\CreateExerciseLis;
use Workdo\Webhook\Listeners\CreateExpenseLis;
use Workdo\Webhook\Listeners\CreateFeeCasePaymentLis;
use Workdo\Webhook\Listeners\CreateFeeLis;
use Workdo\Webhook\Listeners\CreateFeePaymentLis;
use Workdo\Webhook\Listeners\CreateFeeRecieptLis;
use Workdo\Webhook\Listeners\CreateFeeRecieveLis;
use Workdo\Webhook\Listeners\CreateFileLis;
use Workdo\Webhook\Listeners\CreateFixAssetLis;
use Workdo\Webhook\Listeners\CreateFixEquipmentCategory;
use Workdo\Webhook\Listeners\CreateFixEquipmentComponentsLis;
use Workdo\Webhook\Listeners\CreateFixEquipmentConsumablesLis;
use Workdo\Webhook\Listeners\CreateFixEquipmentLicence;
use Workdo\Webhook\Listeners\CreateFixEquipmentLocationLis;
use Workdo\Webhook\Listeners\CreateFixEquipmentStatus;
use Workdo\Webhook\Listeners\CreateFleetCustomerLis;
use Workdo\Webhook\Listeners\CreateFleetInsuranceLis;
use Workdo\Webhook\Listeners\CreateFleetMaintenanceLis;
use Workdo\Webhook\Listeners\CreateFleetPaymentLis;
use Workdo\Webhook\Listeners\CreateFreightBookingRequestLis;
use Workdo\Webhook\Listeners\CreateFreightContainerLis;
use Workdo\Webhook\Listeners\CreateFreightCustomerLis;
use Workdo\Webhook\Listeners\CreateFreightPriceLis;
use Workdo\Webhook\Listeners\CreateFreightServiceLis;
use Workdo\Webhook\Listeners\CreateFreightShippingInvoiceLis;
use Workdo\Webhook\Listeners\CreateFreightShippingOrderLis;
use Workdo\Webhook\Listeners\CreateFreightShippingRouteLis;
use Workdo\Webhook\Listeners\CreateFuelLis;
use Workdo\Webhook\Listeners\CreateFuelTypeLis;
use Workdo\Webhook\Listeners\CreateGarageCategoryLis;
use Workdo\Webhook\Listeners\CreateGarageVehicleLis;
use Workdo\Webhook\Listeners\CreateGymTrainerLis;
use Workdo\Webhook\Listeners\CreateHealthLis;
use Workdo\Webhook\Listeners\CreateHearingLis;
use Workdo\Webhook\Listeners\CreateHighCourtLis;
use Workdo\Webhook\Listeners\CreateHolidayLis;
use Workdo\Webhook\Listeners\CreateHospitalAppointmentLis;
use Workdo\Webhook\Listeners\CreateHospitalBedLis;
use Workdo\Webhook\Listeners\CreateHospitalMedicineLis;
use Workdo\Webhook\Listeners\CreateHotelCustomerLis;
use Workdo\Webhook\Listeners\CreateHotelLis;
use Workdo\Webhook\Listeners\CreateHotelServiceLis;
use Workdo\Webhook\Listeners\CreateInquiryLis;
use Workdo\Webhook\Listeners\CreateInspectionListLis;
use Workdo\Webhook\Listeners\CreateInspectionRequestLis;
use Workdo\Webhook\Listeners\CreateInspectionVehicleLis;
use Workdo\Webhook\Listeners\CreateinsuranceClaimLis;
use Workdo\Webhook\Listeners\CreateinsuranceClaimPaymentLis;
use Workdo\Webhook\Listeners\CreateinsuranceInvoiceLis;
use Workdo\Webhook\Listeners\CreateInsuranceLis;
use Workdo\Webhook\Listeners\CreateInterviewScheduleLis;
use Workdo\Webhook\Listeners\CreateInvoiceLis;
use Workdo\Webhook\Listeners\CreateJobApplicationLis;
use Workdo\Webhook\Listeners\CreateJobCardLis;
use Workdo\Webhook\Listeners\CreateJobLis;
use Workdo\Webhook\Listeners\CreateLabPatientLis;
use Workdo\Webhook\Listeners\CreateLabRequestLis;
use Workdo\Webhook\Listeners\CreateLabTestLis;
use Workdo\Webhook\Listeners\CreateLeadLis;
use Workdo\Webhook\Listeners\CreateLedgerAccountLis;
use Workdo\Webhook\Listeners\CreateLocationLis;
use Workdo\Webhook\Listeners\CreateMachineLis;
use Workdo\Webhook\Listeners\CreateMaintenanceLis;
use Workdo\Webhook\Listeners\CreateManageMarksLis;
use Workdo\Webhook\Listeners\CreateManufacturerLis;
use Workdo\Webhook\Listeners\CreateManufacturingLis;
use Workdo\Webhook\Listeners\CreateMeasurementLis;
use Workdo\Webhook\Listeners\CreateMedicalAppoinmentLis;
use Workdo\Webhook\Listeners\CreateMedicalRecordsLis;
use Workdo\Webhook\Listeners\CreateMedicineCategoryLis;
use Workdo\Webhook\Listeners\CreateMeeingHubMeetingLis;
use Workdo\Webhook\Listeners\CreateMeeingHubMeetingMinuteLis;
use Workdo\Webhook\Listeners\CreateMeeingHubNoteLis;
use Workdo\Webhook\Listeners\CreateMeeingHubTaskLis;
use Workdo\Webhook\Listeners\CreateMeetingLis;
use Workdo\Webhook\Listeners\CreateMembershipPlanLis;
use Workdo\Webhook\Listeners\CreateMenuSelectionLis;
use Workdo\Webhook\Listeners\CreateMilestoneLis;
use Workdo\Webhook\Listeners\CreateMilkInventoryLis;
use Workdo\Webhook\Listeners\CreateMonthlyPayslipLis;
use Workdo\Webhook\Listeners\CreateMovieCastLis;
use Workdo\Webhook\Listeners\CreateMovieCrewLis;
use Workdo\Webhook\Listeners\CreateMovieEventLis;
use Workdo\Webhook\Listeners\CreateMovieShowLis;
use Workdo\Webhook\Listeners\CreateMusicClassLis;
use Workdo\Webhook\Listeners\CreateMusicInstrumentLis;
use Workdo\Webhook\Listeners\CreateMusicLessonLis;
use Workdo\Webhook\Listeners\CreateMusicStudentLis;
use Workdo\Webhook\Listeners\CreateMusicTeacherLis;
use Workdo\Webhook\Listeners\CreateNewspaperAdsLis;
use Workdo\Webhook\Listeners\CreateNewspaperAgentLis;
use Workdo\Webhook\Listeners\CreateNewspaperCategoryLis;
use Workdo\Webhook\Listeners\CreateNewspaperDistributionsLis;
use Workdo\Webhook\Listeners\CreateNewspaperInvoiceLis;
use Workdo\Webhook\Listeners\CreateNewspaperJournalistInfoLis;
use Workdo\Webhook\Listeners\CreateNewspaperJournalistLis;
use Workdo\Webhook\Listeners\CreateNewspaperJournalistTypeLis;
use Workdo\Webhook\Listeners\CreateNewspaperLis;
use Workdo\Webhook\Listeners\CreateNewspaperTaxLis;
use Workdo\Webhook\Listeners\CreateNewspaperTypeLis;
use Workdo\Webhook\Listeners\CreateNewspaperVarientLis;
use Workdo\Webhook\Listeners\CreateNutritionLis;
use Workdo\Webhook\Listeners\CreateOrUpdateFreightShippingServiceLis;
use Workdo\Webhook\Listeners\CreatePackagingLis;
use Workdo\Webhook\Listeners\CreatePageOptionLis;
use Workdo\Webhook\Listeners\CreateParentLis;
use Workdo\Webhook\Listeners\CreateParkingLis;
use Workdo\Webhook\Listeners\CreatePatientCardLis;
use Workdo\Webhook\Listeners\CreatePatientLis;
use Workdo\Webhook\Listeners\CreatePaymentCarPurchaseLis;
use Workdo\Webhook\Listeners\CreatePaymentCarSaleLis;
use Workdo\Webhook\Listeners\CreatePaymentDiagnosisLis;
use Workdo\Webhook\Listeners\CreatePaymentInvoiceLis;
use Workdo\Webhook\Listeners\CreatePaymentLis;
use Workdo\Webhook\Listeners\CreatePaymentRetainerLis;
use Workdo\Webhook\Listeners\CreatePersonDetailLis;
use Workdo\Webhook\Listeners\CreatePmsLis;
use Workdo\Webhook\Listeners\CreatePolicyLis;
use Workdo\Webhook\Listeners\CreatepolicyTypeLis;
use Workdo\Webhook\Listeners\CreatePortfolioCategoryLis;
use Workdo\Webhook\Listeners\CreatePortfolioLis;
use Workdo\Webhook\Listeners\CreatePreDefinedKitLis;
use Workdo\Webhook\Listeners\CreateProjectLis;
use Workdo\Webhook\Listeners\CreatePropertyInvoiceLis;
use Workdo\Webhook\Listeners\CreatePropertyInvoicePaymentLis;
use Workdo\Webhook\Listeners\CreatePropertyLis;
use Workdo\Webhook\Listeners\CreatePropertyUnitLis;
use Workdo\Webhook\Listeners\CreateProposalLis;
use Workdo\Webhook\Listeners\CreatePublicTicketLis;
use Workdo\Webhook\Listeners\CreatePurchaseLis;
use Workdo\Webhook\Listeners\CreatePurchaseOrderLis;
use Workdo\Webhook\Listeners\CreateQuoteLis;
use Workdo\Webhook\Listeners\CreateRattingLis;
use Workdo\Webhook\Listeners\CreateRawMaterialLis;
use Workdo\Webhook\Listeners\CreateRentalLis;
use Workdo\Webhook\Listeners\CreateRepairOrderRequestLis;
use Workdo\Webhook\Listeners\CreateRepairPartLis;
use Workdo\Webhook\Listeners\CreateRepairRequestLis;
use Workdo\Webhook\Listeners\CreateRetainerLis;
use Workdo\Webhook\Listeners\CreateRevenueLis;
use Workdo\Webhook\Listeners\CreateRoomBookingLis;
use Workdo\Webhook\Listeners\CreateRoomFacilityLis;
use Workdo\Webhook\Listeners\CreateRoomFeatureLis;
use Workdo\Webhook\Listeners\CreateRoomLis;
use Workdo\Webhook\Listeners\CreateRotaLis;
use Workdo\Webhook\Listeners\CreateSaleOrderLis;
use Workdo\Webhook\Listeners\CreateSalesAgentLis;
use Workdo\Webhook\Listeners\CreateSalesAgentProgramLis;
use Workdo\Webhook\Listeners\CreateSalesInvoiceLis;
use Workdo\Webhook\Listeners\CreateSalesOrderLis;
use Workdo\Webhook\Listeners\CreateSchoolEmployeeLis;
use Workdo\Webhook\Listeners\CreateSchoolHomeworkLis;
use Workdo\Webhook\Listeners\CreateSchoolParentLis;
use Workdo\Webhook\Listeners\CreateSchoolStudentLis;
use Workdo\Webhook\Listeners\CreateSeasonLis;
use Workdo\Webhook\Listeners\CreateSeatingTemplateDetailLis;
use Workdo\Webhook\Listeners\CreateSeatingTemplateLis;
use Workdo\Webhook\Listeners\CreateSelectionLis;
use Workdo\Webhook\Listeners\CreateServiceLis;
use Workdo\Webhook\Listeners\CreateShowTypeLis;
use Workdo\Webhook\Listeners\CreateSideMenuBuilderLis;
use Workdo\Webhook\Listeners\CreateSkillLis;
use Workdo\Webhook\Listeners\CreateSlotLis;
use Workdo\Webhook\Listeners\CreateSlotTypeLis;
use Workdo\Webhook\Listeners\CreateSpecializationLis;
use Workdo\Webhook\Listeners\CreateSpreadsheetLis;
use Workdo\Webhook\Listeners\CreateSubjectLis;
use Workdo\Webhook\Listeners\CreateSupplierLis;
use Workdo\Webhook\Listeners\CreateTaskCommentLis;
use Workdo\Webhook\Listeners\CreateTaskLis;
use Workdo\Webhook\Listeners\CreateTaxLis;
use Workdo\Webhook\Listeners\CreateTenantLis;
use Workdo\Webhook\Listeners\CreateTestContentLis;
use Workdo\Webhook\Listeners\CreateTestUnitLis;
use Workdo\Webhook\Listeners\CreateTicketLis;
use Workdo\Webhook\Listeners\CreateTimetableLis;
use Workdo\Webhook\Listeners\CreateTimeTrackerLis;
use Workdo\Webhook\Listeners\CreateToDoLis;
use Workdo\Webhook\Listeners\CreateTourBookingLis;
use Workdo\Webhook\Listeners\CreateTourBookingPaymentLis;
use Workdo\Webhook\Listeners\CreateTourDetailLis;
use Workdo\Webhook\Listeners\CreateTourInquiryLis;
use Workdo\Webhook\Listeners\CreateTourLis;
use Workdo\Webhook\Listeners\CreateTrainerLis;
use Workdo\Webhook\Listeners\CreateTrainingLis;
use Workdo\Webhook\Listeners\CreateTransportTypeLis;
use Workdo\Webhook\Listeners\CreateUserLis;
use Workdo\Webhook\Listeners\CreateVehicleBookingLis;
use Workdo\Webhook\Listeners\CreatevehicleBrandLis;
use Workdo\Webhook\Listeners\CreateVehicleColorLis;
use Workdo\Webhook\Listeners\CreateVehicleLis;
use Workdo\Webhook\Listeners\CreateVehicleRouteLis;
use Workdo\Webhook\Listeners\CreatevehicleTypeLis;
use Workdo\Webhook\Listeners\CreateVendorLis;
use Workdo\Webhook\Listeners\CreateVideoHubCommentLis;
use Workdo\Webhook\Listeners\CreateVideoHubVideoLis;
use Workdo\Webhook\Listeners\CreateVisitorLis;
use Workdo\Webhook\Listeners\CreateVisitReasonLis;
use Workdo\Webhook\Listeners\CreateWardLis;
use Workdo\Webhook\Listeners\CreateWarehouseLis;
use Workdo\Webhook\Listeners\CreateWasteCollectionRequestLis;
use Workdo\Webhook\Listeners\CreateWeightLis;
use Workdo\Webhook\Listeners\CreateWoocommerceCategoryLis;
use Workdo\Webhook\Listeners\CreateWoocommerceProductLis;
use Workdo\Webhook\Listeners\CreateWoocommerceTaxLis;
use Workdo\Webhook\Listeners\CreateWorkflowLis;
use Workdo\Webhook\Listeners\CreateWorkorderLis;
use Workdo\Webhook\Listeners\CreateWorkoutPlanLis;
use Workdo\Webhook\Listeners\CreateWorkrequestLis;
use Workdo\Webhook\Listeners\CreateZoommeetingLis;
use Workdo\Webhook\Listeners\CretaeRepairInvoiceLis;
use Workdo\Webhook\Listeners\DealMovedLis;
use Workdo\Webhook\Listeners\DoubleEntryLis;
use Workdo\Webhook\Listeners\DuplicateDocumentTemplateLis;
use Workdo\Webhook\Listeners\DuplicateRentalLis;
use Workdo\Webhook\Listeners\LaundryLocationCreateLis;
use Workdo\Webhook\Listeners\LaundryRequestCreateLis;
use Workdo\Webhook\Listeners\LaundryServicesCreateLis;
use Workdo\Webhook\Listeners\LeadConvertDealLis;
use Workdo\Webhook\Listeners\LeadMovedLis;
use Workdo\Webhook\Listeners\MedicineCategoryCreateLis;
use Workdo\Webhook\Listeners\MedicineCreateLis;
use Workdo\Webhook\Listeners\MedicineTypeCreateLis;
use Workdo\Webhook\Listeners\MobileServiceAssignTechnicianLis;
use Workdo\Webhook\Listeners\MobileServiceCreateLis;
use Workdo\Webhook\Listeners\MobileServicePendingRequestAcceptLis;
use Workdo\Webhook\Listeners\MobileServicePendingRequestRejectLis;
use Workdo\Webhook\Listeners\MobileServiceRequestInvoiceCreateLis;
use Workdo\Webhook\Listeners\MobileServiceRequestTrackingStatusCreateLis;
use Workdo\Webhook\Listeners\PharmacyBillCreateLis;
use Workdo\Webhook\Listeners\PharmacyInvoiceCreateLis;
use Workdo\Webhook\Listeners\RejectinsuranceClaimLis;
use Workdo\Webhook\Listeners\ReplyPublicTicketLis;
use Workdo\Webhook\Listeners\ReplyTicketLis;
use Workdo\Webhook\Listeners\SalesAgentOrderCreateLis;
use Workdo\Webhook\Listeners\SalesAgentRequestAcceptLis;
use Workdo\Webhook\Listeners\SalesAgentRequestRejectLis;
use Workdo\Webhook\Listeners\StatusChangeProposalLis;
use Workdo\Webhook\Listeners\StoreWorkflowLis;
use Workdo\Webhook\Listeners\SuperAdminMenuListener;
use Workdo\Webhook\Listeners\SuperAdminSettingListener;
use Workdo\Webhook\Listeners\SuperAdminSettingMenuListener;
use Workdo\Webhook\Listeners\ToDoStageSystemSetupLis;
use Workdo\Webhook\Listeners\UpdateChidcareLis;
use Workdo\Webhook\Listeners\UpdateFreightShippingLis;
use Workdo\Webhook\Listeners\UpdatePortfolioStatusLis;
use Workdo\Webhook\Listeners\UpdateTaskStageLis;
use Workdo\Webhook\Listeners\UpdateWorkingHoursLis;
use Workdo\Webhook\Listeners\WastecollectionConvertedToTripLis;
use Workdo\Webhook\Listeners\WasteCollectionRequestAcceptLis;
use Workdo\Webhook\Listeners\WasteCollectionRequestRejectLis;
use Workdo\Webhook\Listeners\WasteInspectionStatusUpdateLis;
use Workdo\WordpressWoocommerce\Events\CreateWoocommerceCategory;
use Workdo\WordpressWoocommerce\Events\CreateWoocommerceProduct;
use Workdo\WordpressWoocommerce\Events\CreateWoocommerceTax;
use Workdo\Workflow\Events\CreateWorkflow;
use Workdo\Workflow\Events\WorkflowWebhook;
use Workdo\YooKassa\Events\YooKassaPaymentStatus;
use Workdo\ZoomMeeting\Events\CreateZoommeeting;

class EventServiceProvider extends Provider
{
    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    protected $listen = [
        CreatePaymentInvoice::class => [
            CreatePaymentInvoiceLis::class,
        ],
        SentInvoice::class => [
            CreatePaymentInvoiceLis::class,
        ],
        PaymentDestroyInvoice::class => [
            CreatePaymentInvoiceLis::class,
        ],
        StatusChangeProposal::class => [
            StatusChangeProposalLis::class,
        ],
        SentProposal::class => [
            StatusChangeProposalLis::class,
        ],
        StripePaymentStatus::class => [
            CompanyPaymentLis::class,
        ],
        MolliePaymentStatus::class => [
            CompanyPaymentLis::class,
        ],
        PaystackPaymentStatus::class => [
            CompanyPaymentLis::class,
        ],
        RazorpayPaymentStatus::class => [
            CompanyPaymentLis::class,
        ],
        SkrillPaymentStatus::class => [
            CompanyPaymentLis::class,
        ],
        IyzipayPaymentStatus::class => [
            CompanyPaymentLis::class,
        ],
        PaypalPaymentStatus::class => [
            CompanyPaymentLis::class,
        ],
        PaytabPaymentStatus::class => [
            CompanyPaymentLis::class,
        ],
        CoingatePaymentStatus::class => [
            CompanyPaymentLis::class,
        ],
        PaytmPaymentStatus::class => [
            CompanyPaymentLis::class,
        ],
        MercadoPaymentStatus::class => [
            CompanyPaymentLis::class,
        ],
        FlutterwavePaymentStatus::class => [
            CompanyPaymentLis::class,
        ],
        PayfastPaymentStatus::class => [
            CompanyPaymentLis::class,
        ],
        ToyyibpayPaymentStatus::class => [
            CompanyPaymentLis::class,
        ],
        YooKassaPaymentStatus::class => [
            CompanyPaymentLis::class,
        ],
        SSpayPaymentStatus::class => [
            CompanyPaymentLis::class,
        ],
        PaytrPaymentStatus::class => [
            CompanyPaymentLis::class,
        ],
        AamarPaymentStatus::class => [
            CompanyPaymentLis::class,
        ],
        CashfreePaymentStatus::class => [
            CompanyPaymentLis::class,
        ],
        BenefitPaymentStatus::class => [
            CompanyPaymentLis::class,
        ],
        PhonePePaymentStatus::class => [
            CompanyPaymentLis::class,
        ],
        PayHerePaymentStatus::class => [
            CompanyPaymentLis::class,
        ],
        PaiementProPaymentStatus::class => [
            CompanyPaymentLis::class,
        ],
        CreatePaymentRetainer::class => [
            CreatePaymentRetainerLis::class,
        ],
        PaymentDestroyRetainer::class => [
            CreatePaymentRetainerLis::class,
        ],
        StatusChangeProposal::class => [
            StatusChangeProposalLis::class,
        ],
        SentProposal::class => [
            StatusChangeProposalLis::class,
        ],
        CreateHotel::class => [
            CreateHotelLis::class,
        ],
        UpdateHotel::class => [
            CreateHotelLis::class,
        ],
        WasteCollectionRequestAccept::class => [
            WasteCollectionRequestAcceptLis::class,
        ],
        WasteCollectionRequestReject::class => [
            WasteCollectionRequestRejectLis::class,
        ],
        SendInsuraceMail::class => [
            AcceptInsuraceLis::class,
        ],
        AcceptinsuranceClaim::class => [
            AcceptinsuranceClaimLis::class,
        ],
        AppointmentStatus::class => [
            AppointmentStatusLis::class,
        ],
        AssignActivityCultivation::class => [
            AssignActivityCultivationLis::class,
        ],
        CateringCreateEvent::class => [
            CateringCreateEventLis::class,
        ],
        ChangeStatusAgricultureCultivation::class => [
            ChangeStatusAgricultureCultivationLis::class,
        ],
        ChangeStatusNewspaperInvoice::class => [
            ChangeStatusNewspaperInvoiceLis::class,
        ],
        CompanySettingEvent::class => [
            CompanySettingListener::class,
        ],
        CompanySettingMenuEvent::class => [
            CompanySettingMenuListener::class,
        ],
        ConvertDocumentTemplate::class => [
            ConvertDocumentTemplateLis::class,
        ],
        ConvertToEmployee::class => [
            ConvertToEmployeeLis::class,
        ],
        CopyContractTemplate::class => [
            CopyContractTemplateLis::class,
        ],
        CreateAccessories::class => [
            CreateAccessoriesLis::class,
        ],
        CreateAdmission::class => [
            CreateAdmissionLis::class,
        ],
        CreateAdvocate::class => [
            CreateAdvocateLis::class,
        ],
        CreateAgricultureActivities::class => [
            CreateAgricultureActivitiesLis::class,
        ],
        CreateAgricultureCanal::class => [
            CreateAgricultureCanalLis::class,
        ],
        CreateAgricultureClaimType::class => [
            CreateAgricultureClaimTypeLis::class,
        ],
        CreateAgricultureCrop::class => [
            CreateAgricultureCropLis::class,
        ],
        CreateAgricultureCultivation::class => [
            CreateAgricultureCultivationLis::class,
        ],
        CreateAgricultureCycles::class => [
            CreateAgricultureCyclesLis::class,
        ],
        CreateAgricultureDepartment::class => [
            CreateAgricultureDepartmentLis::class,
        ],
        CreateAgricultureEquipment::class => [
            CreateAgricultureEquipmentLis::class,
        ],
        CreateAgricultureOffices::class => [
            CreateAgricultureOfficesLis::class,
        ],
        CreateAgricultureProcess::class => [
            CreateAgricultureProcessLis::class,
        ],
        CreateAgricultureSeason::class => [
            CreateAgricultureSeasonLis::class,
        ],
        CreateAgricultureSeasonType::class => [
            CreateAgricultureSeasonTypeLis::class,
        ],
        CreateAgricultureServiceProduct::class => [
            CreateAgricultureServiceProductLis::class,
        ],
        CreateAgricultureServices::class => [
            CreateAgricultureServicesLis::class,
        ],
        CreateAgricultureUser::class => [
            CreateAgricultureUserLis::class,
        ],
        CreateAgriculturefleet::class => [
            CreateAgriculturefleetLis::class,
        ],
        CreateAnimal::class => [
            CreateAnimalLis::class,
        ],
        CreateAnnouncement::class => [
            CreateAnnouncementLis::class,
        ],
        CreateAppointment::class => [
            CreateAppointmentLis::class,
        ],
        CreateAppointments::class => [
            CreateAppointmentsLis::class,
        ],
        CreateArticle::class => [
            CreateArticleLis::class,
        ],
        CreateAudit::class => [
            CreateAuditLis::class,
        ],
        CreateAvailability::class => [
            CreateAvailabilityLis::class,
        ],
        CreateAvtivity::class => [
            CreateAvtivityLis::class,
        ],
        CreateAward::class => [
            CreateAwardLis::class,
        ],
        CreateBeautyBooking::class => [
            CreateBeautyBookingLis::class,
        ],
        CreateBeautyService::class => [
            CreateBeautyServiceLis::class,
        ],
        CreateBedType::class => [
            CreateBedTypeLis::class,
        ],
        CreateBillItemMaterial::class => [
            CreateBillItemMaterialLis::class,
        ],
        CreateBill::class => [
            CreateBillLis::class,
        ],
        CreateBillOfMaterial::class => [
            CreateBillOfMaterialLis::class,
        ],
        CreateBlog::class => [
            CreateBlogLis::class,
        ],
        CreateBodyPart::class => [
            CreateBodyPartLis::class,
        ],
        CreateBook::class => [
            CreateBookLis::class,
        ],
        CreateBookingCoupon::class => [
            CreateBookingCouponLis::class,
        ],
        CreateBooking::class => [
            CreateBookingLis::class,
        ],
        CreateBreeding::class => [
            CreateBreedingLis::class,
        ],
        CreateBug::class => [
            CreateBugLis::class,
        ],
        CreateBusiness::class => [
            CreateBusinessLis::class,
        ],
        CreateBusinessProcessMapping::class => [
            CreateBusinessProcessMappingLis::class,
        ],
        CreateCallList::class => [
            CreateCallListLis::class,
        ],
        CreateCarPurchase::class => [
            CreateCarPurchaseLis::class,
        ],
        CreateCarSale::class => [
            CreateCarSaleLis::class,
        ],
        CreateCaseInitiator::class => [
            CreateCaseInitiatorLis::class,
        ],
        CreateCase::class => [
            CreateCaseLis::class,
        ],
        CreateCastType::class => [
            CreateCastTypeLis::class,
        ],
        CreateCategory::class => [
            CreateCategoryLis::class,
        ],
        CreateCateringCustomer::class => [
            CreateCateringCustomerLis::class,
        ],
        CreateCateringInvoice::class => [
            CreateCateringInvoiceLis::class,
        ],
        CreateCertificate::class => [
            CreateCertificateLis::class,
        ],
        CreateChallengeCategory::class => [
            CreateChallengeCategoryLis::class,
        ],
        CreateChallenge::class => [
            CreateChallengeLis::class,
        ],
        CreateChildAttendance::class => [
            CreateChildAttendanceLis::class,
        ],
        CreateChild::class => [
            CreateChildLis::class,
        ],
        CreateClass::class => [
            CreateClassLis::class,
        ],
        CreateClassroom::class => [
            CreateClassroomLis::class,
        ],
        CreateCleaningBooking::class => [
            CreateCleaningBookingLis::class,
        ],
        CreateCleaningInvoice::class => [
            CreateCleaningInvoiceLis::class,
        ],
        CreateCleaningTeam::class => [
            CreateCleaningTeamLis::class,
        ],
        CreateCmmspos::class => [
            CreateCmmsposLis::class,
        ],
        CreateCollectionCenter::class => [
            CreateCollectionCenterLis::class,
        ],
        CreateCommissionPlan::class => [
            CreateCommissionPlanLis::class,
        ],
        CreateCommissionReceipt::class => [
            CreateCommissionReceiptLis::class,
        ],
        CreateCompanyPolicy::class => [
            CreateCompanyPolicyLis::class,
        ],
        CreateComponent::class => [
            CreateComponentLis::class,
        ],
        CreateConsignment::class => [
            CreateConsignmentLis::class,
        ],
        CreateProduct::class => [
            CreateConsignmentProduct::class,
        ],
        CreateContact::class => [
            CreateContactLis::class,
        ],
        CreateContract::class => [
            CreateContractLis::class,
        ],
        CreateContractTemplate::class => [
            CreateContractTemplateLis::class,
        ],
        Couriercreate::class => [
            CreateCourieLis::class,
        ],
        Manualpaymentdatastore::class => [
            CreateCourierManualPayment::class,
        ],
        Courierpackagecategorycreate::class => [
            CreateCourierPackageCategoryLis::class,
        ],
        Courierservicetypecreate::class => [
            CreateCourierServiceType::class,
        ],
        Couriertrackingstatuscreate::class => [
            CreateCourierTrackingStatusLis::class,
        ],
        Courierbranchcreate::class => [
            CreateCourierbranchLis::class,
        ],
        CreateCourse::class => [
            CreateCourseLis::class,
        ],
        CreateCourt::class => [
            CreateCourtLis::class,
        ],
        CreateCreativity::class => [
            CreateCreativityLis::class,
        ],
        CreateCreativityStage::class => [
            CreateCreativityStageLis::class,
        ],
        CreateCreativityStatus::class => [
            CreateCreativityStatusLis::class,
        ],
        CreateCustomPage::class => [
            CreateCustomPageLis::class,
        ],
        CreateCustomer::class => [
            CreateCustomerLis::class,
        ],
        CreateDailyMilkSheet::class => [
            CreateDailyMilkSheetLis::class,
        ],
        CreateDeal::class => [
            CreateDealLis::class,
        ],
        CreateDealershipProduct::class => [
            CreateDealershipProductLis::class,
        ],
        CreateDefectsAndRepairs::class => [
            CreateDefectsAndRepairsLis::class,
        ],
        CreateDepreciation::class => [
            CreateDepreciationLis::class,
        ],
        CreateDiagnosis::class => [
            CreateDiagnosisLis::class,
        ],
        CreateDiet::class => [
            CreateDietLis::class,
        ],
        CreateDivision::class => [
            CreateDivisionLis::class,
        ],
        CreateDoctor::class => [
            CreateDoctorLis::class,
        ],
        CreateDocuments::class => [
            CreateDocumentLis::class,
        ],
        CreateDocumentTemplate::class => [
            CreateDocumentTemplateLis::class,
        ],
        CreateDocumentsType::class => [
            CreateDocumentTypeLis::class,
        ],
        CreateDriver::class => [
            CreateDriverLis::class,
        ],
        CreateEquipment::class => [
            CreateEquipmentLis::class,
        ],
        CreateEventDetail::class => [
            CreateEventDetailLis::class,
        ],
        CreateEvent::class => [
            CreateEventLis::class,
        ],
        CreateExamGrade::class => [
            CreateExamGradeLis::class,
        ],
        CreateExamHall::class => [
            CreateExamHallLis::class,
        ],
        CreateExamHallReceipt::class => [
            CreateExamHallReceiptLis::class,
        ],
        CreateExamList::class => [
            CreateExamListLis::class,
        ],
        CreateExamTimeTable::class => [
            CreateExamTimeTableLis::class,
        ],
        CreateExercise::class => [
            CreateExerciseLis::class,
        ],
        CreateExpense::class => [
            CreateExpenseLis::class,
        ],
        CreateFeeCasePayment::class => [
            CreateFeeCasePaymentLis::class,
        ],
        CreateFee::class => [
            CreateFeeLis::class,
        ],
        CreateFeePayment::class => [
            CreateFeePaymentLis::class,
        ],
        CreateFeeReciept::class => [
            CreateFeeRecieptLis::class,
        ],
        CreateFeeRecieve::class => [
            CreateFeeRecieveLis::class,
        ],
        CreateFile::class => [
            CreateFileLis::class,
        ],
        CreateAsset::class => [
            CreateFixAssetLis::class,
        ],
        CreateFixCategory::class => [
            CreateFixEquipmentCategory::class,
        ],
        CreateFixEquipmentComponents::class => [
            CreateFixEquipmentComponentsLis::class,
        ],
        CreateConsumables::class => [
            CreateFixEquipmentConsumablesLis::class,
        ],
        CreateLicence::class => [
            CreateFixEquipmentLicence::class,
        ],
        CreateFixEquipmentLocation::class => [
            CreateFixEquipmentLocationLis::class,
        ],
        CreateStatus::class => [
            CreateFixEquipmentStatus::class,
        ],
        CreateFleetCustomer::class => [
            CreateFleetCustomerLis::class,
        ],
        CreateFleetInsurance::class => [
            CreateFleetInsuranceLis::class,
        ],
        CreateMaintenances::class => [
            CreateFleetMaintenanceLis::class,
        ],
        CreateFleetPayment::class => [
            CreateFleetPaymentLis::class,
        ],
        CreateFreightBookingRequest::class => [
            CreateFreightBookingRequestLis::class,
        ],
        CreateFreightContainer::class => [
            CreateFreightContainerLis::class,
        ],
        CreateFreightCustomer::class => [
            CreateFreightCustomerLis::class,
        ],
        CreateFreightPrice::class => [
            CreateFreightPriceLis::class,
        ],
        CreateFreightService::class => [
            CreateFreightServiceLis::class,
        ],
        CreateFreightShippingInvoice::class => [
            CreateFreightShippingInvoiceLis::class,
        ],
        CreateFreightShippingOrder::class => [
            CreateFreightShippingOrderLis::class,
        ],
        CreateFreightShippingRoute::class => [
            CreateFreightShippingRouteLis::class,
        ],
        CreateFuel::class => [
            CreateFuelLis::class,
        ],
        CreateFuelType::class => [
            CreateFuelTypeLis::class,
        ],
        CreateGarageCategory::class => [
            CreateGarageCategoryLis::class,
        ],
        CreateGarageVehicle::class => [
            CreateGarageVehicleLis::class,
        ],
        CreateGymTrainer::class => [
            CreateGymTrainerLis::class,
        ],
        CreateHealth::class => [
            CreateHealthLis::class,
        ],
        CreateHearing::class => [
            CreateHearingLis::class,
        ],
        CreateHighCourt::class => [
            CreateHighCourtLis::class,
        ],
        CreateHolidays::class => [
            CreateHolidayLis::class,
        ],
        CreateHospitalAppointment::class => [
            CreateHospitalAppointmentLis::class,
        ],
        CreateHospitalBed::class => [
            CreateHospitalBedLis::class,
        ],
        CreateHospitalMedicine::class => [
            CreateHospitalMedicineLis::class,
        ],
        CreateHotelCustomer::class => [
            CreateHotelCustomerLis::class,
        ],
        CreateHotelService::class => [
            CreateHotelServiceLis::class,
        ],
        CreateInquiry::class => [
            CreateInquiryLis::class,
        ],
        CreateInspectionList::class => [
            CreateInspectionListLis::class,
        ],
        CreateInspectionRequest::class => [
            CreateInspectionRequestLis::class,
        ],
        CreateInspectionVehicle::class => [
            CreateInspectionVehicleLis::class,
        ],
        CreateInsurance::class => [
            CreateInsuranceLis::class,
        ],
        CreateInterviewSchedule::class => [
            CreateInterviewScheduleLis::class,
        ],
        CreateInvoice::class => [
            CreateInvoiceLis::class,
        ],
        CreateJobApplication::class => [
            CreateJobApplicationLis::class,
        ],
        CreateJobCard::class => [
            CreateJobCardLis::class,
        ],
        CreateJob::class => [
            CreateJobLis::class,
        ],
        CreateLabPatient::class => [
            CreateLabPatientLis::class,
        ],
        CreateLabRequest::class => [
            CreateLabRequestLis::class,
        ],
        CreateLabTest::class => [
            CreateLabTestLis::class,
        ],
        CreateLead::class => [
            CreateLeadLis::class,
        ],
        CreateLedgerAccount::class => [
            CreateLedgerAccountLis::class,
        ],
        CreateLocation::class => [
            CreateLocationLis::class,
        ],
        CreateMachine::class => [
            CreateMachineLis::class,
        ],
        CreateMaintenance::class => [
            CreateMaintenanceLis::class,
        ],
        CreateManageMarks::class => [
            CreateManageMarksLis::class,
        ],
        CreateManufacturer::class => [
            CreateManufacturerLis::class,
        ],
        CreateManufacturing::class => [
            CreateManufacturingLis::class,
        ],
        CreateMeasurement::class => [
            CreateMeasurementLis::class,
        ],
        CreateMedicalAppoinment::class => [
            CreateMedicalAppoinmentLis::class,
        ],
        CreateMedicalRecords::class => [
            CreateMedicalRecordsLis::class,
        ],
        CreateMedicineCategory::class => [
            CreateMedicineCategoryLis::class,
        ],
        CreateMeeingHubMeeting::class => [
            CreateMeeingHubMeetingLis::class,
        ],
        CreateMeeingHubMeetingMinute::class => [
            CreateMeeingHubMeetingMinuteLis::class,
        ],
        CreateMeeingHubNote::class => [
            CreateMeeingHubNoteLis::class,
        ],
        CreateMeeingHubTask::class => [
            CreateMeeingHubTaskLis::class,
        ],
        CreateMeeting::class => [
            CreateMeetingLis::class,
        ],
        CreateMembershipPlan::class => [
            CreateMembershipPlanLis::class,
        ],
        CreateMenuSelection::class => [
            CreateMenuSelectionLis::class,
        ],
        CreateMilestone::class => [
            CreateMilestoneLis::class,
        ],
        CreateMilkInventory::class => [
            CreateMilkInventoryLis::class,
        ],
        CreateMonthlyPayslip::class => [
            CreateMonthlyPayslipLis::class,
        ],
        CreateMovieCast::class => [
            CreateMovieCastLis::class,
        ],
        CreateMovieCrew::class => [
            CreateMovieCrewLis::class,
        ],
        CreateMovieEvent::class => [
            CreateMovieEventLis::class,
        ],
        CreateMovieShow::class => [
            CreateMovieShowLis::class,
        ],
        CreateMusicClass::class => [
            CreateMusicClassLis::class,
        ],
        CreateMusicInstrument::class => [
            CreateMusicInstrumentLis::class,
        ],
        CreateMusicLesson::class => [
            CreateMusicLessonLis::class,
        ],
        CreateMusicStudent::class => [
            CreateMusicStudentLis::class,
        ],
        CreateMusicTeacher::class => [
            CreateMusicTeacherLis::class,
        ],
        CreateNewspaperAds::class => [
            CreateNewspaperAdsLis::class,
        ],
        CreateNewspaperAgent::class => [
            CreateNewspaperAgentLis::class,
        ],
        CreateNewspaperCategory::class => [
            CreateNewspaperCategoryLis::class,
        ],
        CreateNewspaperDistributions::class => [
            CreateNewspaperDistributionsLis::class,
        ],
        CreateNewspaperInvoice::class => [
            CreateNewspaperInvoiceLis::class,
        ],
        CreateNewspaperJournalistInfo::class => [
            CreateNewspaperJournalistInfoLis::class,
        ],
        CreateNewspaperJournalist::class => [
            CreateNewspaperJournalistLis::class,
        ],
        CreateNewspaperJournalistType::class => [
            CreateNewspaperJournalistTypeLis::class,
        ],
        CreateNewspaper::class => [
            CreateNewspaperLis::class,
        ],
        CreateNewspaperTax::class => [
            CreateNewspaperTaxLis::class,
        ],
        CreateNewspaperType::class => [
            CreateNewspaperTypeLis::class,
        ],
        CreateNewspaperVarient::class => [
            CreateNewspaperVarientLis::class,
        ],
        CreateNutrition::class => [
            CreateNutritionLis::class,
        ],
        CreateOrUpdateFreightShippingService::class => [
            CreateOrUpdateFreightShippingServiceLis::class,
        ],
        CreatePackaging::class => [
            CreatePackagingLis::class,
        ],
        CreatePageOption::class => [
            CreatePageOptionLis::class,
        ],
        CreateParent::class => [
            CreateParentLis::class,
        ],
        CreateParking::class => [
            CreateParkingLis::class,
        ],
        CreatePatientCard::class => [
            CreatePatientCardLis::class,
        ],
        CreatePatient::class => [
            CreatePatientLis::class,
        ],
        CreatePaymentCarPurchase::class => [
            CreatePaymentCarPurchaseLis::class,
        ],
        CreatePaymentCarSale::class => [
            CreatePaymentCarSaleLis::class,
        ],
        CreatePaymentDiagnosis::class => [
            CreatePaymentDiagnosisLis::class,
        ],
        CreatePayment::class => [
            CreatePaymentLis::class,
        ],
        CreatePersonDetail::class => [
            CreatePersonDetailLis::class,
        ],
        CreatePms::class => [
            CreatePmsLis::class,
        ],
        CreatePolicy::class => [
            CreatePolicyLis::class,
        ],
        PortfolioCategoryCreate::class => [
            CreatePortfolioCategoryLis::class,
        ],
        CreatePortfolio::class => [
            CreatePortfolioLis::class,
        ],
        CreatePreDefinedKit::class => [
            CreatePreDefinedKitLis::class,
        ],
        CreateProject::class => [
            CreateProjectLis::class,
        ],
        CreatePropertyInvoice::class => [
            CreatePropertyInvoiceLis::class,
        ],
        CreatePropertyInvoicePayment::class => [
            CreatePropertyInvoicePaymentLis::class,
        ],
        CreateProperty::class => [
            CreatePropertyLis::class,
        ],
        CreatePropertyUnit::class => [
            CreatePropertyUnitLis::class,
        ],
        CreateProposal::class => [
            CreateProposalLis::class,
        ],
        CreatePublicTicket::class => [
            CreatePublicTicketLis::class,
        ],
        CreatePurchase::class => [
            CreatePurchaseLis::class,
        ],
        CreatePurchaseOrder::class => [
            CreatePurchaseOrderLis::class,
        ],
        CreateQuote::class => [
            CreateQuoteLis::class,
        ],
        CreateRatting::class => [
            CreateRattingLis::class,
        ],
        CreateRawMaterial::class => [
            CreateRawMaterialLis::class,
        ],
        CreateRental::class => [
            CreateRentalLis::class,
        ],
        CreateRepairOrderRequest::class => [
            CreateRepairOrderRequestLis::class,
        ],
        CreateRepairPart::class => [
            CreateRepairPartLis::class,
        ],
        CreateRepairRequest::class => [
            CreateRepairRequestLis::class,
        ],
        CreateRetainer::class => [
            CreateRetainerLis::class,
        ],
        CreateRevenue::class => [
            CreateRevenueLis::class,
        ],
        CreateRoomBooking::class => [
            CreateRoomBookingLis::class,
        ],
        CreateRoomFacility::class => [
            CreateRoomFacilityLis::class,
        ],
        CreateRoomFeature::class => [
            CreateRoomFeatureLis::class,
        ],
        CreateRoom::class => [
            CreateRoomLis::class,
        ],
        CreateRota::class => [
            CreateRotaLis::class,
        ],
        CreateSaleOrder::class => [
            CreateSaleOrderLis::class,
        ],
        SalesAgentCreate::class => [
            CreateSalesAgentLis::class,
        ],
        SalesAgentProgramCreate::class => [
            CreateSalesAgentProgramLis::class,
        ],
        CreateSalesInvoice::class => [
            CreateSalesInvoiceLis::class,
        ],
        CreateSalesOrder::class => [
            CreateSalesOrderLis::class,
        ],
        CreateSchoolEmployee::class => [
            CreateSchoolEmployeeLis::class,
        ],
        CreateSchoolHomework::class => [
            CreateSchoolHomeworkLis::class,
        ],
        CreateSchoolParent::class => [
            CreateSchoolParentLis::class,
        ],
        CreateSchoolStudent::class => [
            CreateSchoolStudentLis::class,
        ],
        CreateSeason::class => [
            CreateSeasonLis::class,
        ],
        CreateSeatingTemplateDetail::class => [
            CreateSeatingTemplateDetailLis::class,
        ],
        CreateSeatingTemplate::class => [
            CreateSeatingTemplateLis::class,
        ],
        CreateSelection::class => [
            CreateSelectionLis::class,
        ],
        CreateService::class => [
            CreateServiceLis::class,
        ],
        CreateShowType::class => [
            CreateShowTypeLis::class,
        ],
        CreateSideMenuBuilder::class => [
            CreateSideMenuBuilderLis::class,
        ],
        CreateSkill::class => [
            CreateSkillLis::class,
        ],
        CreateSlot::class => [
            CreateSlotLis::class,
        ],
        CreateSlotType::class => [
            CreateSlotTypeLis::class,
        ],
        CreateSpecialization::class => [
            CreateSpecializationLis::class,
        ],
        CreateSpreadsheet::class => [
            CreateSpreadsheetLis::class,
        ],
        CreateSubject::class => [
            CreateSubjectLis::class,
        ],
        CreateSupplier::class => [
            CreateSupplierLis::class,
        ],
        CreateTaskComment::class => [
            CreateTaskCommentLis::class,
        ],
        CreateTask::class => [
            CreateTaskLis::class,
        ],
        CreateTax::class => [
            CreateTaxLis::class,
        ],
        CreateTenant::class => [
            CreateTenantLis::class,
        ],
        CreateTestContent::class => [
            CreateTestContentLis::class,
        ],
        CreateTestUnit::class => [
            CreateTestUnitLis::class,
        ],
        CreateTicket::class => [
            CreateTicketLis::class,
        ],
        CreateTimeTracker::class => [
            CreateTimeTrackerLis::class,
        ],
        CreateTimetable::class => [
            CreateTimetableLis::class,
        ],
        CreateToDo::class => [
            CreateToDoLis::class,
        ],
        CreateTourBooking::class => [
            CreateTourBookingLis::class,
        ],
        CreateTourBookingPayment::class => [
            CreateTourBookingPaymentLis::class,
        ],
        CreateTourDetail::class => [
            CreateTourDetailLis::class,
        ],
        CreateTourInquiry::class => [
            CreateTourInquiryLis::class,
        ],
        CreateTour::class => [
            CreateTourLis::class,
        ],
        CreateTrainer::class => [
            CreateTrainerLis::class,
        ],
        CreateTraining::class => [
            CreateTrainingLis::class,
        ],
        CreateTransportType::class => [
            CreateTransportTypeLis::class,
        ],
        CreateUser::class => [
            CreateUserLis::class,
        ],
        CreateVehicleBooking::class => [
            CreateVehicleBookingLis::class,
        ],
        CreateVehicleColor::class => [
            CreateVehicleColorLis::class,
        ],
        CreateVehicle::class => [
            CreateVehicleLis::class,
        ],
        CreateVehicleRoute::class => [
            CreateVehicleRouteLis::class,
        ],
        CreateVendor::class => [
            CreateVendorLis::class,
        ],
        CreateVideoHubComment::class => [
            CreateVideoHubCommentLis::class,
        ],
        CreateVideoHubVideo::class => [
            CreateVideoHubVideoLis::class,
        ],
        CreateVisitReason::class => [
            CreateVisitReasonLis::class,
        ],
        CreateVisitor::class => [
            CreateVisitorLis::class,
        ],
        CreateWard::class => [
            CreateWardLis::class,
        ],
        CreateWarehouse::class => [
            CreateWarehouseLis::class,
        ],
        WasteCollectionRequestCreate::class => [
            CreateWasteCollectionRequestLis::class,
        ],
        CreateWeight::class => [
            CreateWeightLis::class,
        ],
        CreateWoocommerceCategory::class => [
            CreateWoocommerceCategoryLis::class,
        ],
        CreateWoocommerceProduct::class => [
            CreateWoocommerceProductLis::class,
        ],
        CreateWoocommerceTax::class => [
            CreateWoocommerceTaxLis::class,
        ],
        WorkflowWebhook::class => [
            CreateWorkflowLis::class,
        ],
        CreateWorkorder::class => [
            CreateWorkorderLis::class,
        ],
        CreateWorkoutPlan::class => [
            CreateWorkoutPlanLis::class,
        ],
        CreateWorkrequest::class => [
            CreateWorkrequestLis::class,
        ],
        CreateZoommeeting::class => [
            CreateZoommeetingLis::class,
        ],
        CreateinsuranceClaim::class => [
            CreateinsuranceClaimLis::class,
        ],
        CreateinsuranceClaimPayment::class => [
            CreateinsuranceClaimPaymentLis::class,
        ],
        CreateinsuranceInvoice::class => [
            CreateinsuranceInvoiceLis::class,
        ],
        CreatepolicyType::class => [
            CreatepolicyTypeLis::class,
        ],
        CreatevehicleBrand::class => [
            CreatevehicleBrandLis::class,
        ],
        CreatevehicleType::class => [
            CreatevehicleTypeLis::class,
        ],
        CretaeRepairInvoice::class => [
            CretaeRepairInvoiceLis::class,
        ],
        DealMoved::class => [
            DealMovedLis::class,
        ],
        CreateJournalAccount::class => [
            DoubleEntryLis::class,
        ],
        DuplicateDocumentTemplate::class => [
            DuplicateDocumentTemplateLis::class,
        ],
        DuplicateRental::class => [
            DuplicateRentalLis::class,
        ],
        LaundryLocationCreate::class => [
            LaundryLocationCreateLis::class,
        ],
        LaundryRequestCreate::class => [
            LaundryRequestCreateLis::class,
        ],
        LaundryServicesCreate::class => [
            LaundryServicesCreateLis::class,
        ],
        LeadConvertDeal::class => [
            LeadConvertDealLis::class,
        ],
        LeadMoved::class => [
            LeadMovedLis::class,
        ],
        MedicineCategoryCreate::class => [
            MedicineCategoryCreateLis::class,
        ],
        MedicineCreate::class => [
            MedicineCreateLis::class,
        ],
        MedicineTypeCreate::class => [
            MedicineTypeCreateLis::class,
        ],
        MobileServiceAssignTechnician::class => [
            MobileServiceAssignTechnicianLis::class,
        ],
        MobileServiceCreate::class => [
            MobileServiceCreateLis::class,
        ],
        MobileServicePendingRequestAccept::class => [
            MobileServicePendingRequestAcceptLis::class,
        ],
        MobileServicePendingRequestReject::class => [
            MobileServicePendingRequestRejectLis::class,
        ],
        MobileServiceRequestInvoiceCreate::class => [
            MobileServiceRequestInvoiceCreateLis::class,
        ],
        MobileServiceRequestTrackingStatusCreate::class => [
            MobileServiceRequestTrackingStatusCreateLis::class,
        ],
        PharmacyBillCreate::class => [
            PharmacyBillCreateLis::class,
        ],
        PharmacyInvoiceCreate::class => [
            PharmacyInvoiceCreateLis::class,
        ],
        RejectinsuranceClaim::class => [
            RejectinsuranceClaimLis::class,
        ],
        ReplyPublicTicket::class => [
            ReplyPublicTicketLis::class,
        ],
        ReplyTicket::class => [
            ReplyTicketLis::class,
        ],
        SalesAgentOrderCreate::class => [
            SalesAgentOrderCreateLis::class,
        ],
        SalesAgentRequestAccept::class => [
            SalesAgentRequestAcceptLis::class,
        ],
        SalesAgentRequestReject::class => [
            SalesAgentRequestRejectLis::class,
        ],
        CreateWorkflow::class => [
            StoreWorkflowLis::class,
        ],
        SuperAdminMenuEvent::class => [
            SuperAdminMenuListener::class,
        ],
        SuperAdminSettingEvent::class => [
            SuperAdminSettingListener::class,
        ],
        SuperAdminSettingMenuEvent::class => [
            SuperAdminSettingMenuListener::class,
        ],
        ToDoStageSystemSetup::class => [
            ToDoStageSystemSetupLis::class,
        ],
        UpdateChidcare::class => [
            UpdateChidcareLis::class,
        ],
        UpdateFreightShipping::class => [
            UpdateFreightShippingLis::class,
        ],
        UpdatePortfolioStatus::class => [
            UpdatePortfolioStatusLis::class,
        ],
        UpdateTaskStage::class => [
            UpdateTaskStageLis::class,
        ],
        UpdateWorkingHours::class => [
            UpdateWorkingHoursLis::class,
        ],
        WasteInspectionStatusUpdate::class => [
            WasteInspectionStatusUpdateLis::class,
        ],
        WastecollectionConvertedToTrip::class => [
            WastecollectionConvertedToTripLis::class,
        ],
    ];

    /**
     * Get the listener directories that should be used to discover events.
     *
     * @return array
     */
    protected function discoverEventsWithin()
    {
        return [
            __DIR__ . '/../Listeners',
        ];
    }
}
