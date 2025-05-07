import {
  UserType,
  Privilege,
  PartyRole,
  IPartyMembership,
  IUserData,
} from "./types";

/**
 * Classe abstraite décrivant un utilisateur générique
 */
export abstract class User implements IUserData {
  readonly id: string;
  firstName: string;
  lastName: string;
  birthDate: Date;
  sex: "M" | "F" | "Other";
  discordId?: string;
  pseudonym?: string;

  isRegistered: boolean;
  firstRegistrationDate?: Date;

  memberships: IPartyMembership[];

  constructor(data: IUserData) {
    this.id                    = data.id;
    this.firstName             = data.firstName;
    this.lastName              = data.lastName;
    this.birthDate             = data.birthDate;
    this.sex                   = data.sex;
    this.discordId             = data.discordId;
    this.pseudonym             = data.pseudonym;

    this.isRegistered          = data.isRegistered;
    this.firstRegistrationDate = data.firstRegistrationDate;
    this.memberships           = data.memberships ?? [];
  }

  /** Type concret de l’utilisateur */
  abstract get userType(): UserType;

  /** Est connecté (true pour tout sauf Visitor) */
  get isConnected(): boolean {
    return this.userType !== UserType.Visitor;
  }

  /** Nom complet */
  get fullName(): string {
    return `${this.firstName} ${this.lastName}`;
  }

  /** Âge en années (entier) */
  get age(): number {
    const diff = Date.now() - this.birthDate.getTime();
    return Math.floor(diff / (1000 * 60 * 60 * 24 * 365.25));
  }

  /** Ancienneté en années d’inscription */
  get seniorityYears(): number {
    if (!this.firstRegistrationDate) return 0;
    const diff = Date.now() - this.firstRegistrationDate.getTime();
    return Math.floor(diff / (1000 * 60 * 60 * 24 * 365.25));
  }

  /** Parties dont l’utilisateur fait partie */
  get parties(): string[] {
    return this.memberships.map(m => m.partyId);
  }

  /** Parties filtrées par rôle */
  getPartiesByRole(role: PartyRole): string[] {
    return this.memberships
      .filter(m => m.role === role)
      .map(m => m.partyId);
  }

  /** Est MJ d’une partie */
  isGameMaster(partyId: string): boolean {
    return this.memberships.some(
      m => m.partyId === partyId && m.role === PartyRole.GameMaster
    );
  }

  /** Est joueur d’une partie */
  isPlayer(partyId: string): boolean {
    return this.memberships.some(
      m => m.partyId === partyId && m.role === PartyRole.Player
    );
  }

  /** Droits associés */
  abstract get privileges(): Privilege[];
}

/** Visiteur non connecté */
export class Visitor extends User {
  get userType() { return UserType.Visitor; }
  get privileges(): Privilege[] {
    return ["VIEW_PUBLIC_CONTENT"];
  }
}

/** Connecté mais non inscrit (droits restreints) */
export class NonRegisteredUser extends User {
  constructor(data: IUserData) {
    super({ ...data, isRegistered: false, firstRegistrationDate: undefined });
  }
  get userType() { return UserType.NonRegistered; }
  get privileges(): Privilege[] {
    return ["VIEW_PUBLIC_CONTENT", "POST_ARTICLES"];
  }
}

/** Utilisateur inscrit à l’association */
export class RegisteredUser extends User {
  constructor(data: IUserData) {
    if (!data.isRegistered || !data.firstRegistrationDate) {
      throw new Error(
        "RegisteredUser requiert isRegistered=true et firstRegistrationDate"
      );
    }
    super(data);
  }
  get userType() { return UserType.Registered; }
  get privileges(): Privilege[] {
    return [
      "VIEW_PUBLIC_CONTENT",
      "VIEW_PRIVATE_CONTENT",
      "POST_ARTICLES",
    ];
  }
}

/** Administrateur (droits étendus) */
// TODO: Check the user is a real ADMIN
export class Administrator extends RegisteredUser {
  get userType() { return UserType.Administrator; }
  get privileges(): Privilege[] {
    return [
      ...super.privileges,
      "MANAGE_USERS",
      "ADMIN_PANEL",
    ];
  }
}

export const getCurrentUser = (): User => {
  return( 
    new RegisteredUser({
      id: "3",
      firstName: "Alice",
      lastName: "Bernard",
      birthDate: new Date("1992-07-10"),
      sex: "F",
      discordId: "Alice#2025",
      pseudonym: "AliB",
      isRegistered: true,                       // obligatoire
      firstRegistrationDate: new Date("2024-02-01"), // obligatoire
      memberships: [
        { partyId: "party123", role: PartyRole.Player },
        { partyId: "101", role: PartyRole.GameMaster },
      ],
    })
  );
};