export enum UserType {
    Visitor            = "VISITOR",           // non connecté
    NonRegistered      = "NON_REGISTERED",    // connecté, droits restreints
    Registered         = "REGISTERED",        // connecté, membre de l’asso
    Administrator      = "ADMINISTRATOR",     // connecté, droits élevés
  }
  
  export enum PartyRole {
    Player     = "PLAYER",
    GameMaster = "GAMEMASTER",
  }
  
  export type Privilege =
    | "VIEW_PUBLIC_CONTENT"
    | "VIEW_PRIVATE_CONTENT"
    | "POST_ARTICLES"
    | "MANAGE_USERS"
    | "ADMIN_PANEL";
  
  export interface IPartyMembership {
    partyId: string;
    role: PartyRole;
  }
  
  export interface IUserData {
    id: string;
    firstName: string;
    lastName: string;
    birthDate: Date;
    sex: "M" | "F" | "Other";
    discordId?: string;
    pseudonym?: string;
  
    // Inscription
    isRegistered: boolean;
    firstRegistrationDate?: Date;
  
    // Inscriptions aux parties de JDR
    memberships?: IPartyMembership[];
  }
