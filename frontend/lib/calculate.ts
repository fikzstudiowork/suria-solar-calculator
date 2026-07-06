export interface CalcConfig {
  tariffRate: number;
  costPerKwp: number;
  sunHours: number;
  derate: number;
  offsetPercent: number;
  exposureFactors: Record<string, number>;
}

export const DEFAULT_CONFIG: CalcConfig = {
  tariffRate: 0.571,
  costPerKwp: 4200,
  sunHours: 4.5,
  derate: 0.85,
  offsetPercent: 0.9,
  exposureFactors: {
    Excellent: 1.0,
    Good: 1.15,
    Moderate: 1.3,
  },
};

export interface CalcInputs {
  roofType: string;
  storeys: string;
  meterPhase: string;
  usagePattern: string;
  monthlyBill: number;
  propertyType: string;
  roofExposure: string;
}

export interface CalcResults {
  recommendedKwp: number;
  estMonthlySavings: number;
  estAnnualSavings: number;
  paybackYears: number;
  estSystemCost: number;
  annualGenerationKwh: number;
  annualGenerationMwh: number;
  savings25YearMin: number;
  savings25YearMax: number;
  co2TonsPerYear: number;
  treesEquivalent: number;
  carsOffRoad: number;
}

const ROOF_EXPOSURE_MAP: Record<string, string> = {
  "Simple tile roof": "Good",
  "Tile roof with dormer windows": "Moderate",
  "Metal roof": "Good",
  "Concrete roof": "Moderate",
  "Mixed roof": "Moderate",
  "I'm not sure / others": "Moderate",
};

const STOREYS_PROPERTY_MAP: Record<string, string> = {
  "Single storey": "Terrace House",
  "2 storeys": "Semi-Detached",
  "3 storeys": "Semi-Detached",
  "4 storeys and above": "Small Commercial",
};

function usageOffset(usagePattern: string, base: number): number {
  if (usagePattern.includes("mornings and evenings")) return base * 0.82;
  return base;
}

function meterMultiplier(meterPhase: string): number {
  if (meterPhase === "3 phase") return 1.12;
  return 1.0;
}

export function deriveInputs(partial: {
  roofType: string;
  storeys: string;
  meterPhase: string;
  usagePattern: string;
  monthlyBill: number;
}): CalcInputs {
  return {
    ...partial,
    roofExposure: ROOF_EXPOSURE_MAP[partial.roofType] ?? "Moderate",
    propertyType: STOREYS_PROPERTY_MAP[partial.storeys] ?? "Terrace House",
  };
}

export function calculate(
  inputs: CalcInputs,
  config: CalcConfig = DEFAULT_CONFIG
): CalcResults {
  const exposureFactor =
    config.exposureFactors[inputs.roofExposure] ?? 1.15;
  const meterMult = meterMultiplier(inputs.meterPhase);
  const offset = usageOffset(inputs.usagePattern, config.offsetPercent);

  const kwhMonth = inputs.monthlyBill / config.tariffRate;
  const recommendedKwp =
    (kwhMonth * exposureFactor * meterMult) /
    (config.sunHours * 30 * config.derate);

  const annualGenerationKwh =
    recommendedKwp * config.sunHours * 365 * config.derate;
  const generationValueMonthly =
    (annualGenerationKwh / 12) * config.tariffRate * offset;

  const estMonthlySavings = Math.min(
    inputs.monthlyBill,
    generationValueMonthly
  );
  const estAnnualSavings = estMonthlySavings * 12;
  const estSystemCost = recommendedKwp * config.costPerKwp;
  const paybackYears =
    estAnnualSavings > 0 ? estSystemCost / estAnnualSavings : 0;

  const savings25YearMin = estAnnualSavings * 22;
  const savings25YearMax = estAnnualSavings * 26;
  const co2TonsPerYear = (annualGenerationKwh * 0.00059);
  const treesEquivalent = Math.round(annualGenerationKwh / 105);
  const carsOffRoad = Math.max(1, Math.round(co2TonsPerYear / 4.6));

  return {
    recommendedKwp: round(recommendedKwp, 2),
    estMonthlySavings: round(estMonthlySavings, 2),
    estAnnualSavings: round(estAnnualSavings, 2),
    paybackYears: round(paybackYears, 1),
    estSystemCost: round(estSystemCost, 2),
    annualGenerationKwh: round(annualGenerationKwh, 0),
    annualGenerationMwh: round(annualGenerationKwh / 1000, 2),
    savings25YearMin: round(savings25YearMin, 0),
    savings25YearMax: round(savings25YearMax, 0),
    co2TonsPerYear: round(co2TonsPerYear, 2),
    treesEquivalent,
    carsOffRoad,
  };
}

function round(value: number, decimals: number): number {
  const factor = Math.pow(10, decimals);
  return Math.round(value * factor) / factor;
}

export const MALAYSIAN_STATES = [
  "Johor",
  "Kedah",
  "Kelantan",
  "Melaka",
  "Negeri Sembilan",
  "Pahang",
  "Perak",
  "Perlis",
  "Pulau Pinang",
  "Sabah",
  "Sarawak",
  "Selangor",
  "Terengganu",
  "Wilayah Persekutuan Kuala Lumpur",
  "Wilayah Persekutuan Labuan",
  "Wilayah Persekutuan Putrajaya",
] as const;

export const BILL_CHIPS = [200, 300, 400, 500] as const;

export const WHATSAPP_NUMBER = "60127075391";
export const PRIVACY_POLICY_URL = "https://suriainfiniti.com/privacy-policy/";
export const CONTACT_EMAIL = "taufik@suriainfiniti.com";
export const CONTACT_PHONE = "+60 12-707 5391";

export function buildWhatsAppUrl(
  name: string,
  results: CalcResults,
  inputs: CalcInputs,
  whatsappNumber = WHATSAPP_NUMBER
): string {
  const text = encodeURIComponent(
    `Hi Suria Infiniti, I'm ${name}. I used your solar calculator and got:\n` +
      `- System size: ${results.recommendedKwp} kWp\n` +
      `- Est. monthly savings: RM ${results.estMonthlySavings}\n` +
      `- Roof: ${inputs.roofType}\n` +
      `- Storeys: ${inputs.storeys}\n` +
      `I'd like to get an exact quote.`
  );
  return `https://wa.me/${whatsappNumber}?text=${text}`;
}

export function formatRm(value: number, compact = false): string {
  if (compact && value >= 1000) {
    return `RM ${(value / 1000).toFixed(1)}K`;
  }
  return `RM ${value.toLocaleString("en-MY", { maximumFractionDigits: 0 })}`;
}
