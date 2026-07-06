export const ROOF_TYPES = [
  "Simple tile roof",
  "Tile roof with dormer windows",
  "Metal roof",
  "Concrete roof",
  "Mixed roof",
  "I'm not sure / others",
] as const;

export const STOREYS = [
  "Single storey",
  "2 storeys",
  "3 storeys",
  "4 storeys and above",
] as const;

export const METER_PHASES = [
  "Single phase",
  "3 phase",
  "I don't know",
] as const;

export const USAGE_PATTERNS = [
  "Mostly during the mornings and evenings",
  "Equally throughout the day",
] as const;

export const WIZARD_STEPS = 6;

export const STEP_TITLES: Record<number, string> = {
  1: "My rooftop type is…",
  2: "My house is…",
  3: "My meter box is…",
  4: "I use my electricity…",
  5: "My monthly electricity bill is…",
  6: "Your solar savings estimate is ready",
};
